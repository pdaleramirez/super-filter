import vSelect from 'vue-select';
import ComboBox from './components/ComboBox';

window.axios = require('axios');
window.Vue = require('vue');

Vue.component('v-select', vSelect);
Vue.component('combo-box', ComboBox);

