import vSelect from 'vue-select';
import ComboBox from './components/ComboBox';
import Cards from './components/Cards';


window.axios = require('axios');
window.Vue = require('vue');

Vue.component('v-select', vSelect);
Vue.component('combo-box', ComboBox);
Vue.component('cards', Cards);

window.qs = require('qs');
