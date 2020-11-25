import Vue from 'vue';
import VueRouter from 'vue-router';
import Tboard from './views/Tboard';
import Home from './views/Home';
import UserShow from './views/Users/Show';

Vue.use(VueRouter);

// Instantiate
export default new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/', name: 'talk', component: Tboard,
            meta: {title: 'Talkboard'}
        },
        {
            path: '/home', name: 'home', component: Home,
            meta: {title: 'Home'}
        },
        {
            path: '/users/:userId', name: 'user.show', component: UserShow,
            meta: {title: 'Profile'}
        },
    ]
});