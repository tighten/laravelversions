import { createApp } from 'vue';
import OutsideClick from './directives/OutsideClick';
import LanguageSelect from './components/LanguageSelect.vue';

const app = createApp({});

app.component('language-select', LanguageSelect);
app.directive('outside-click', OutsideClick);

app.mount('#app');
