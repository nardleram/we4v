<template>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 flex flex-col items-center max-h-screen overflow-x-hidden">
                <modal v-if="show">
                    I was a king
                </modal>
                <div class="relative mb-5">
                    <div class="w-full h-64 overflow-hidden cursor-pointer" @click = "show = !show">
                        <img :src="'/'+user.bkgrnd_image" alt="">
                    </div>
                    <div class="absolute flex items-center -mb-6 bottom-0 left-0 z-20">
                        <div class="w-24 cursor-pointer" @click = "show = !show">
                            <img :src="'/'+user.profile_image" alt="" class="rounded-full object-cover shadow-md h-24 w-24">
                        </div>
                        <p class="font-bold tracking-tighter ml-4 text-we4vBg text-lg">{{ user.username }}</p>
                    </div>
                    <div v-if="$page.props.authUser.username !== $page.props.user.username" class="absolute flex items-center mb-4 bottom-0 right-0 z-20">
                        <button v-if="associationStatus == 'associated'" 
                        class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-we4vGrey-200 text-we4vGrey-600 w-full p-2 border border-we4vGrey-600 mr-2 cursor-default">
                            Associated
                        </button>
                        <div class="flex" v-if="associationStatus == 'pending' && myResponseNeeded">
                            <button 
                            class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-we4vGreen-600 text-we4vGrey-100 w-full p-2 hover:text-white hover:bg-we4vGreen-700 border border-we4vGreen-700 mr-2" @click="acceptAssocRequest()">
                                Accept
                            </button>
                            <button class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-red-700 text-we4vGrey-100 w-full p-2 hover:text-white hover:bg-red-800 border border-red-800 mr-2" @click="rejectAssocRequest()">
                                Reject
                            </button>
                        </div>
                        <button v-else-if="associationStatus == 'pending' && !myResponseNeeded"
                        class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-we4vGrey-700 text-we4vOrange w-full p-2 border border-we4vGrey-800 mr-2 cursor-default">
                            Association pending...
                        </button>
                        <button v-if="associationStatus == 'none'" 
                        class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-we4vGrey-200 text-we4vGreen-700 w-full p-2 hover:bg-we4vGrey-300 border border-we4vGrey-400 mr-2"
                        @click="dispatchAssocRequest()">
                            Connect
                        </button>
                    </div>
                </div>

                <div class="w-full">
                    <p v-if="posts_status !== 'success'">Retrieving posts...</p>
                    <div class="text-center w-1/2" v-else-if="posts_status === 'success' && posts.length < 1">
                        <p >No posts by {{ $page.props.user.username }} found.</p>
                        <inertia-link v-if="$page.props.authUser.id === $page.props.user.id" :href="route('talkboard')" as="button">
                            <p class="text-we4vBlue font-bold cursor-pointer hover:underline">Add a post?</p>
                        </inertia-link>
                    </div>
                    
                    <Post class="text-left" v-else v-for="(post, postKey) in posts" :key="postKey" :post="post" />
                </div>
            </div>
        </template>
    </app-layout>
</template>

<script>
import Post from './components/Post';
import AppLayout from '@/Layouts/AppLayout';

export default {
    name: 'Show',

    components: {
        Post,
        AppLayout,
    },
    props: [
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
        acceptAssocRequest() {
            let payload = {
                'id': this.$page.props.myPendingAssocReqs.id,
                'status': 1
            }
            this.associationStatus = 'associated';
            this.$inertia.post('/associate-request-response', payload);
        },
        rejectAssocRequest() {
            let payload = {
                'id': this.$page.props.myPendingAssocReqs.id,
                'status': 0
            }
            this.associationStatus = 'none';
            this.$inertia.post('/associate-request-response', payload);
        }
    }
}
</script>