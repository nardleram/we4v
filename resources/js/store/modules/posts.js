const state = {
    posts: null,
    postsStatus: null,
    postMessage: '',

};

const getters = {
    posts: state => {
        return state.posts;
    },
    postsStatus: state => {
        return {
            postsStatus: state.postsStatus,
        }
    },
    postMessage: state => {
        return state.postMessage;
    },
};

const actions = {
    fetchTboardPosts({commit, state}) {
        commit('setPostsStatus', 'loading');

        axios.get('/api/posts')
            .then(res => {
                commit('setPosts', res.data);
                commit('setPostsStatus', 'success');
            })
            .catch(error => {
                commit('setPostsStatus', 'Error');
            })
    },
    fetchUserPosts({commit, dispatch}, userId) {
        commit('setPostsStatus', 'loading');

        axios.get('/api/users/' + userId + '/posts')
            .then(res => {
                commit('setPosts', res.data);
                commit('setPostsStatus', 'success');
            })
            .catch(error => {
                console.log('fethcUserPosts: DB be sleepin. Got back squat shyte.');
                commit('setPostsStatus', 'error');
            });
    },
    postMessage({commit, state}) {
        commit('setPostsStatus', 'loading');

        axios.post('/api/posts', { body: state.postMessage })
            .then(res => {
                commit('pushPost', res.data);
                commit('setPostsStatus', 'success');
                commit('updateMessage', '');
            })
            .catch(error => {
                commit('setPostsStatus', 'Error storing new post');
            })
    },
    postComment({commit, state}, data) {
        axios.post('/api/posts/' + data.postId + '/comment', { body: data.body})
            .then(res => {
                commit('pushComment', {comments: res.data, postKey: data.postKey});
            })
            .catch(error => {
                commit('setPostsStatus', 'Error saving your comment');
            })
    },
    likePost({commit, state}, data) {
        axios.post('/api/posts/' + data.postId + '/like')
            .then(res => {
                commit('pushLike', {likes: res.data, postKey: data.postKey});
            })
            .catch(error => {
                commit('setPostsStatus', 'Error liking this post');
            })
    }
};

const mutations = {
    setPosts(state, posts) {
        state.posts = posts;
    },
    setPostsStatus(state, status) {
        state.postsStatus = status;
    },
    updateMessage(state, message) {
        state.postMessage = message;
    },
    pushPost(state, post) {
        state.posts.data.unshift(post);
    },
    pushLike(state, data) {
        state.posts.data[data.postKey].data.attributes.likes = data.likes;
    },
    pushComment(state, data) {
        state.posts.data[data.postKey].data.attributes.comments = data.comments;
    }
};

export default {
    state, getters, actions, mutations,
}