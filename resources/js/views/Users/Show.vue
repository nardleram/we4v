<template>
    <div class="flex flex-col items-center" v-if="user && status.user === 'success'">
        <div class="relative mb-5">
            <div class="w-full h-64 overflow-hidden">
                <UploadableImage
                    alt="User background image"
                    classes="object-cover w-full"
                    image-width="1200" 
                    image-height="500" 
                    page-position="cover" 
                    :user-image="user.data.attributes.cover_image"
                />
            </div>
            <div class="absolute flex items-center -mb-6 bottom-0 left-0 z-20">
                <div class="w-24">
                    <UploadableImage
                        alt="User profile image"
                        classes="object-cover w-24 h-24 ml-2 rounded-full shadow-lg border-4 border-we4vBg"
                        image-width="750" 
                        image-height="750" 
                        page-position="profile" 
                        :user-image="user.data.attributes.profile_image"
                    />
                </div>
                <p class="font-bold tracking-tighter ml-4 text-we4vBg text-lg">{{ user.data.attributes.uname }}</p>
            </div>

            <div class="absolute flex items-center mb-4 bottom-0 right-0 z-20">
                <button v-if="associateButtonText && associateButtonText != 'Accept'" 
                class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-gray-300 text-we4vGrey-600 w-full p-2 hover:bg-we4vGrey-100 border border-we4vGrey-600 mr-2"
                @click="$store.dispatch('sendAssociateRequest', $route.params.userId)">
                    {{ associateButtonText }}
                </button>
                <button v-if="associateButtonText && associateButtonText === 'Accept'" 
                class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-gray-900 text-we4vGrey-100 w-full p-2 hover:bg-we4vGrey-800 border border-we4vOrange mr-2"
                @click="$store.dispatch('acceptAssociateRequest', $route.params.userId)">
                    Accept
                </button>
                <button v-if="associateButtonText && associateButtonText === 'Accept'" 
                class="font-bold text-sm tracking-tight flex justify-center rounded-lg bg-gray-300 text-red-700 w-full p-2 hover:bg-we4vGrey-200 border border-we4vGrey-600 mr-2"
                @click="$store.dispatch('ignoreAssociateRequest', $route.params.userId)">
                    Ignore
                </button>
            </div>
        </div>

        <p v-if="status.posts === 'loading'">Retrieving posts...</p>
        <p v-else-if="status.posts === 'success' && posts.data.length < 1">No posts found for {{ user.data.attributes.uname }}. Add a post?</p>
        <Post v-else v-for="(post, postKey) in posts.data" :key="postKey" :post="post" />
    </div>
</template>

<script>
import Post from '../../components/Post';
import { mapGetters } from 'vuex';
import UploadableImage from '../../components/UploadableImage';

export default {
    name: 'Show',

    components: {
        Post,
        UploadableImage,
    },

    computed: {
        ...mapGetters({
            user: 'user',
            posts: 'posts',
            status: 'status',
            associateButtonText: 'associateButtonText',
        })
    },

    mounted() {
        this.$store.dispatch('fetchUser', this.$route.params.userId);
        this.$store.dispatch('fetchUserPosts', this.$route.params.userId);
    }
}
</script>