<template>
    <modal-backdrop :show="showBackdrop"></modal-backdrop>

    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 ml-1/4 tracking-tight">
                <div class="relative mb-5">
                    <div class="w-full h-64">
                        <img :src="'/storage/'+user.bkgrnd_image" alt="User background image" class="h-64 object-fit">
                    </div>
                    <div class="absolute flex items-center -mb-4 bottom-0 left-0 z-20">
                        <div class="w-24">
                            <img :src="'/storage/'+user.profile_image" alt="User profile image" class="rounded-full object-cover shadow-md h-24 w-24">
                        </div>
                        <p class="font-bold tracking-tighter ml-4 text-we4vBg text-lg">{{ user.username }}</p>
                    </div>
                    <div v-if="$page.props.authUser.username !== $page.props.user.username" class="absolute flex items-center mb-4 bottom-0 right-0 z-20">
                        <button v-if="associationStatus == 'associated'" 
                        class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-we4vBg text-we4vGreen-500 w-full p-2 border border-we4vGreen-600 mr-2 cursor-default">
                            Associated
                        </button>
                        <div class="flex" v-if="associationStatus == 'pending' && myResponseNeeded">
                            <button 
                            class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-we4vGreen-600 text-we4vGrey-100 w-full p-2 hover:text-white hover:bg-we4vGreen-700 border border-we4vGreen-700 mr-2" @click="respondAssocRequest(1)">
                                Accept
                            </button>
                            <button class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-red-700 text-we4vGrey-100 w-full p-2 hover:text-white hover:bg-red-800 border border-red-800 mr-2" @click="respondAssocRequest(0)">
                                Reject
                            </button>
                        </div>
                        <button v-else-if="associationStatus == 'pending' && !myResponseNeeded"
                        class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-we4vGrey-900 text-we4vBlue w-full p-2 border border-we4vDarkBlue mr-2 cursor-default">
                            Association pending...
                        </button>
                        <button v-if="associationStatus == 'none'" 
                        class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-we4vGrey-100 text-we4vGreen-600 w-full p-2 hover:bg-we4vGrey-200 border border-we4vGrey-400 mr-2"
                        @click="dispatchAssocRequest()">
                            Connect
                        </button>
                    </div>
                </div>

                <div class="w-full">
                    <div v-if="myArticles.length > 0" class="w-full mt-8">
                        <h4>My published articles</h4>
                        <div class="text-we4vGrey-800 bg-white p-3 flex flex-col flex-1 shadow-md my-3">
                            <Article v-for="(article, articleKey) in myArticles" :key="articleKey" :article="article" />
                        </div>
                    </div>

                    <p v-if="posts_status !== 'success'">Retrieving posts...</p>
                    <div class="text-center w-1/2" v-else-if="posts_status === 'success' && posts.length < 1">
                        <p>No posts by {{ $page.props.user.username }} found.</p>
                        <inertia-link v-if="$page.props.authUser.id === $page.props.user.id" :href="route('talkboard')" as="button">
                            <p class="text-we4vBlue font-bold cursor-pointer hover:underline">Add a post?</p>
                        </inertia-link>
                    </div>
                    <div v-else>
                        <h4>My posts</h4>
                        <post-user-self class="text-left" v-for="(post, postKey) in posts" :key="postKey" :post="post" />
                    </div>
                </div>
            </div>
        </template>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Article from '../Pages/Components/Article'
import ModalBackdrop from '../Pages/Components/ModalBackdrop'
import manageModals from '../Pages/Composables/manageModals'
import PostUserSelf from './Components/PostUserSelf.vue'

export default {
    name: 'Show',

    components: {
        Article,
        AppLayout,
        ModalBackdrop,
        PostUserSelf,
    },
    props: [
        'myArticles',
        'posts',
        'posts_status',
        'user',
    ],

    data: () => {
        return {
            associationStatus: 'none',
            myResponseNeeded: false,
            show: false,
        }
    },

    setup() {
        const {
            amOutside, 
            amInside,
            clearModal,
            edit,
            mode,
            nowInside, 
            nowOutside,
            showBackdrop,
        } = manageModals()

        return {
            amOutside, 
            amInside,
            clearModal,
            edit,
            mode,
            nowInside, 
            nowOutside,
            showBackdrop,
        }
    },

    mounted() {
        this.setAssociationStatus(this.$page.props.user.id)
        this.isMyResponseNeeded()
    },

    methods: {
        setAssociationStatus(uid) {
            let myAssociates = this.$page.props.myAssociates
            for (let a = 0 ; a < myAssociates.length ; ++a) {
                console.log(uid + ' ' + myAssociates[a].user_id)
                if (myAssociates[a].user_id === uid) {
                    this.associationStatus = 'associated'
                }
            }
            for (let a = 0 ; a < this.$page.props.myPendingAssocReqs.length ; ++a) {
                if (this.$page.props.myPendingAssocReqs[a].requested_by === uid
                || this.$page.props.myPendingAssocReqs[a].requested_of === uid) {
                    this.associationStatus = 'pending'
                }
            }
        },

        isMyResponseNeeded() {
            for (let p = 0 ; p < this.$page.props.myPendingAssocReqs.length ; ++p) {
                if (this.$page.props.user.id == this.$page.props.myPendingAssocReqs[p].requested_by && this.$page.props.myPendingAssocReqs[p].myResponseNeeded) {
                    this.myResponseNeeded = true;
                }
            }
        },

        dispatchAssocRequest() {
            let payload = {
                'requested_by': this.$page.props.authUser.id,
                'requested_of': this.$page.props.user.id,
            }
            this.associationStatus = 'pending';
            this.$inertia.post('/associate-request', payload);
        },

        respondAssocRequest(status) {
            let id
            this.$page.props.myPendingAssocReqs.forEach(assoc => {
                if (assoc.requested_by === this.$page.props.user.id) {
                    id = assoc.id
                }
            });
            let payload = {
                'id': id,
                'requested_of': this.$page.props.authUser.id,
                'requested_by': this.$page.props.user.id,
                'status': status
            }
            
            this.$inertia.post('/associate-request-response', payload)
            status
            ? this.associationStatus = 'associated'
            : this.associationStatus = 'none'
        }
    }
}
</script>