<template>
    <modal-backdrop :show="showBackdrop"></modal-backdrop>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 ml-1/4 tracking-tight">
                <NewPost @update-posts="addPost($event)" />

                <p v-if="posts_status !== 'success'">Retrieving posts...</p>
                <Post v-else v-for="(post, postKey) in posts" :key="postKey" :post="post" />
            </div>
        </template>
    </app-layout>
</template>
 
<script>
import AppLayout from '@/Layouts/AppLayout'
import NewPost from './Components/NewPost'
import Post from './Components/Post'
import manageModals from '../Pages/Composables/manageModals'
import ModalBackdrop from './Components/ModalBackdrop'

export default {
    name: 'Talkboard',

    props: [
        'posts',
        'posts_status',
    ],

    components: {
        AppLayout,
        Post,
        NewPost,
        ModalBackdrop,
    },

    setup() {
    const {
        amInside,
        amOutside, 
        clearModal,
        nowInside, 
        nowOutside,
        onClickOutside,
        showBackdrop,
    } = manageModals()

    return { amInside, amOutside, clearModal, nowInside, nowOutside, onClickOutside, showBackdrop }
    },

    methods: {
        addPost: function(data) {
            let addedPost = {
                'body': data.body,
                'slug': this.$page.props.authUser.slug,
                'image': data.image,
                'posted_by': this.$page.props.authUser.username,
                'created_at': data.posted_at,
                'num_approvals': 0,
                'num_comments': 0,
                'user_profile_pic': this.$page.props.userProfileImages.profile,
                'post_id': data.id,
                'user_id': this.$page.props.authUser.id,
            }
            this.posts.unshift(addedPost);
        }
    }
}
</script>