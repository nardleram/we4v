<template>
    <div class="flex flex-col items-center py-2">
        <NewPost />

        <p v-if="tboardStatus.postsStatus === 'loading'">Retrieving posts...</p>
        <Post v-else v-for="(post, postKey) in posts.data" :key="postKey" :post="post" />
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import NewPost from '../components/NewPost';
import Post from '../components/Post';
export default {
    name: 'Tboard',

    components: {
        NewPost,
        Post,
    },

    mounted() {
        this.$store.dispatch('fetchTboardPosts');
    },

    computed: {
        ...mapGetters({
            posts: 'posts',
            tboardStatus: 'postsStatus',
        })
    }
}
</script>