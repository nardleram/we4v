<template>
    <div class="bg-white rounded shadow mt-6 w-2/3 overflow-hidden">
        <div class="flex flex-col p-2">
            <div class="flex items-center">
                <div class="w-10 mr-2">
                    <img :src="post.data.attributes.posted_by.data.attributes.profile_image.data.attributes.path" class="rounded-full object-cover h-10 w-10" />
                </div>
                <div>
                    <div class="-mb-1">
                        <small class="font-semibold text-sm tracking-tighter m-0 p-0 leading-3">{{ post.data.attributes.posted_by.data.attributes.uname }}</small>
                    </div>
                    <div>
                        <small class="text-xs tracking-tighter m-0 p-0 leading-3 text-we4vGrey-300">{{ post.data.attributes.posted_at }}</small>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <p class="tracking-tight">{{ post.data.attributes.body }}</p>
            </div>
        </div>

        <div class="w-full" v-if="post.data.attributes.image.length">
            <img :src="post.data.attributes.image" alt="" class="object-cover">
        </div>

        <div class="px-4 flex justify-between text-sm tracking-tight text-we4vGrey-400 mt-2">
            <div class="ml-2">
                <small>{{ post.data.attributes.likes.like_count }} approvals</small>
            </div>
            <div class="ml-2">
                <small>{{ post.data.attributes.comments.comment_count }} comments</small>
            </div>
        </div>

        <div class="flex justify-between m-4">
            <button :class="[post.data.attributes.likes.user_likes_post ? 'text-we4vBlue bg-we4vGrey-900 border-we4vBlue' : 'text-we4vGrey-500 hover:bg-we4vGrey-100 border-we4vGrey-300']" 
            class="font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1"
            @click="$store.dispatch('likePost', {postId: post.data.post_id, postKey: $vnode.key})">
                <span v-if="post.data.attributes.likes.user_likes_post">Approved</span>
                <span v-else>Approve</span>
            </button>
            <button class="font-bold text-sm tracking-tight flex justify-center rounded-lg text-we4vGrey-500 w-full hover:bg-we4vGrey-100 border border-we4vGrey-300 focus:outline-none"
            @click="comments = ! comments">
                Comment
            </button>
        </div>

        <div v-if="comments" class="border-t border-we4vGrey-500 p-4 pt-2">
            <div class="flex">
                <input v-model="commentBody" type="text" name="comment" class="w-full pl-4 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tighter" placeholder="Add comment">
                <transition name="fade">
                    <button v-if="commentBody"
                        @click="$store.dispatch('postComment', { body: commentBody, postId: post.data.post_id, postKey: $vnode.key }); commentBody = ''" 
                        class="bg-we4vGrey-200 rounded-full h-8 w-8 border border-we4vGrey-200 font-bold text-2xl text-we4vBlue ml-2 leading-2">
                        +
                    </button>
                </transition>
            </div>

            <div class="flex my-4 items-center" v-for="(comment, index) in post.data.attributes.comments.data" :key="index">
                <div class="w-10">
                    <img :src="comment.data.attributes.commented_by.data.attributes.profile_image.data.attributes.path" class="rounded-full object-cover min-w-prof-pic w-10 h-10" />
                </div>
                <div class="flex-1 ml-2">
                    <div class="bg-we4vGrey-100 rounded-lg p-2 text-sm tracking-tight">
                        <a :href="'/users/'+comment.data.attributes.commented_by.data.user_id">
                            <small class="text-sm tracking-tighter text-we4vBlue font-bold">{{ comment.data.attributes.commented_by.data.attributes.uname }}</small>
                        </a>
                        <p class="inline">
                            {{ comment.data.attributes.body }}
                        </p>
                    </div>
                    <div>
                        <small class="text-xs tracking-tighter text-we4vGrey-400 pl-2">
                            {{ comment.data.attributes.commented_at }}
                        </small>
                    </div>
                </div>
            </div>

        </div>

    </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    name: 'Post',

    props: [
        'post',
    ],

    data: () => {
        return {
            comments: false,
            commentBody: '',
        }
    },

    computed: {
        ...mapGetters({
            authUser: 'authUser',
        }),
    }
}
</script>

<style scoped>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .7s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0; 
    }
</style>