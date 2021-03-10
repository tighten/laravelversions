/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import OutsideClick from './directives/OutsideClick';

/**
 * Directives
 */
Vue.directive('outside-click', OutsideClick);

/**
 * Components
 */
Vue.component(
    'language-select',
    require('./components/LanguageSelect.vue').default
);

/**
 * Application instance
 */
const app = new Vue({
    el: '#app',
});
