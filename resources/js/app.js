window.axios = require('axios');
window.Vue   = require('vue');
window.qs    = require('qs');
import Paginate from 'vuejs-paginate'
import InfiniteLoading from 'vue-infinite-loading';
Vue.use(InfiniteLoading, { /* options */ });
Vue.use(require('vue-moment'));
Vue.component('paginate', Paginate)
Vue.component('infinite-loading', InfiniteLoading)
