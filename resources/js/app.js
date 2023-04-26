/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';

import Vue from 'vue';

import OutsideClick from './directives/OutsideClick';
import LanguageSelect from './components/LanguageSelect.vue';

/**
 * Directives
 */
Vue.directive('outside-click', OutsideClick);

/**
 * Components
 */
Vue.component('language-select', LanguageSelect);

/**
 * Application instance
 */
const app = new Vue({
    el: '#app',
});
