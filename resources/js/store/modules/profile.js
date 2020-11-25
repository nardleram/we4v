const state = {
    user: null,
    userStatus: null,

};

const getters = {
    user: state => {
        return state.user;
    },
    status: state => {
        return {
            user: state.userStatus,
            posts: state.postsStatus,
        };
    },
    association: state => {
        return state.user.data.attributes.association;
    },
    associateButtonText: (state, getters, rootState) => {
        if (rootState.User.user.data.user_id == state.user.data.user_id) {
            return '';
        } else if (getters.association === null) {
            return 'Request association';
        } else if (getters.association.data.attributes.confirmed_at === null
            && getters.association.data.attributes.associate_id != rootState.User.user.data.user_id) {
            return 'Association pending...';
        } else if (getters.association.data.attributes.confirmed_at != null) {
            return '';
        }

        return 'Accept';
    },
};

const actions = {
    fetchUser({commit, dispatch}, userId) {
        commit('setUserStatus', 'loading');

        axios.get('/api/users/' + userId)
            .then(res => {
                commit('setUser', res.data);
                commit('setUserStatus', 'success');
            })
            .catch(error => {
                commit('setUserStatus', 'error');
            });
    },
    sendAssociateRequest({commit, getters}, associateId) {
        if (getters.associateButtonText != 'Request association') {
            return;
        }
        axios.post('/api/associate-request', {'associate_id': associateId})
            .then(res => {
                commit('setUserAssociation', res.data);
            })
            .catch(error => {
                console.log('associate-request post: ' + error);
            })
    },
    acceptAssociateRequest({commit, state}, userId) {
        axios.post('/api/associate-request-response', {'user_id': userId, 'status': 1})
            .then(res => {
                commit('setUserAssociation', res.data);
            })
            .catch(error => {
                console.log('associate-request-response-yes post: ' + error);
            })
    },
    ignoreAssociateRequest({commit, state}, userId) {
        axios.delete('/api/associate-request-response/delete', { data: { 'user_id': userId } })
            .then(res => {
                commit('setUserAssociation', null);
            })
            .catch(error => {
                console.log('associate-request-response-no post: ' + error);
            })
    },
};

const mutations = {
    setUser(state, user) {
        state.user = user;
    },
    setUserAssociation(state, association) {
        state.user.data.attributes.association = association;
    },
    setUserStatus(state, status) {
        state.userStatus = status;
    },
};

export default {
    state, getters, actions, mutations,
}