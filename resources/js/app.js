require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'

import mytasks from './components/mytasks.vue';
import navbar from './components/navbar.vue';
import home from './components/home.vue';

Vue.component('mytasks', mytasks);
Vue.component('navbar', navbar);
Vue.component('home', home);


Vue.use(VueRouter);

const router = new VueRouter({
	mode: 'history',
  	routes: [
    { 
  	    path: '/',
    		component: home,
    		props: true
    },
    {
      	path: '/mytasks',
      	component: mytasks,
      	props: true
    },
    {
      	path: '*', redirect: '/',
      	component: home,
      	props: true
    }]
})

const app = new Vue({
  router
}).$mount('#app')
