<template>
    <div class="text-we4vGrey-800 bg-white p-3 flex flex-col flex-1 shadow-md mb-2">
        <div class="flex flex-1">
            <div class="mr-3 h-12 w-12">
                <inertia-link :href="route('user-show', post.slug)" as="button">
                    <img :src="'/'+post.user_profile_pic" :alt="post.name" class="rounded-full object-cover h-10 w-10">
                </inertia-link>
            </div>
            <div class="text-we4vBlue w-full font-semibold text-sm flex items-center justify-between">
                <div>
                    <p class="text-we4vBlue">{{ post.posted_by }}, {{ post.created_at }}</p>
                </div>
                <div>
                    <p class="text-we4vBlue text-xs">{{ post.num_approvals }} approvals, {{ post.num_comments }} comments</p>
                </div>
            </div>
        </div>
        <div v-html="post.body" class="tracking-tight text-we4vPost text-we4vGrey-700"></div>

        <div class="w-full mt-2" v-if="post.image">
            <img :src="'/'+post.image" alt="" class="object-cover mx-auto">
        </div>

        <div class="flex justify-between m-4">
            <button :class="[post.user_approves_post ? 'text-we4vBlue bg-we4vGrey-900 border-we4vBlue' : 'text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300']" class="font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1" @click="storeApproval(post.post_id)">
                <span v-if="post.user_approves_post">Approved</span>
                <span v-else>Approve</span>
            </button>
            <button class="font-bold text-sm tracking-tight flex justify-center rounded-lg text-we4vGrey-600 w-full hover:bg-we4vGrey-100 border border-we4vGrey-300 focus:outline-none"
            @click="comments = !comments">
                Comment
            </button>
        </div>

        <div v-if="comments" class="pt-2">
            <div class="flex mb-4">
                <input v-model="commentBody" type="text" name="comment" class="w-full pl-4 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:border-we4vBlue text-sm tracking-tighter" placeholder="Add comment">
                <transition name="fade">
                    <button v-if="commentBody"
                        @click="postComment(commentBody, post.post_id); commentBody = ''" 
                        class="text-we4vBlue ml-3">
                        <small><i class="fas fa-save cursor-pointer text-xl text-we4vBlue"></i></small>
                    </button>
                </transition>
                <div @click="errors = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors">{{ errors }}</div>
            </div>
            <div class="px-3">
                <div v-for="(comment, commentKey) in post.comments" :key="commentKey">
                    <div class="flex flex-1">
                        <div class="mr-3 w-1/12">
                            <inertia-link :href="route('user-show', comment.slug)" as="button">
                                <img :src="'/'+comment.user_profile_pic" :alt="comment.commented_by" class="rounded-full w-8 h-8 object-cover">
                            </inertia-link>
                        </div>
                        <div class="mb-3 w-11/12">
                            <div v-html="comment.body" class="-mb-1 text-we4vComment text-we4vGrey-700"></div>
                            <small class="text-gray-400 tracking-tight text-xs">{{ comment.commented_by }}, {{ comment.commented_at }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
 
<script>
    export default {
        name: 'Post',

        props: [
            'post',
            'comment',
            'user',
            'errors'
        ],

        data: () => {
            return {
                comments: false,
                commentBody: '',
            }
        },

        methods: {
            storeApproval: function (id) {
                this.$inertia.post('/approvals/store', {
                    'id': id,
                    'model': 'App\\Models\\Post'
                })
                if (this.post.user_likes_post) { // User is therefore disapproving
                    this.post.user_likes_post = false;
                    --this.post.num_approvals;
                } else {
                    this.post.user_likes_post = true;
                    ++this.post.num_approvals;
                }
            },

            postComment: function (comment, post_id) {
                this.$inertia.post('/comments/store', {
                    'body': comment,
                    'commentable_id': post_id,
                    'commentable_type': 'App\\Models\\Post',
                    'parent_id': null,
                });
            },
        },

    }
</script>