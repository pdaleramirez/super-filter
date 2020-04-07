if (window.axios === undefined) {
  window.axios = require('axios');
}

if (window.Vue === undefined) {
  window.Vue = require('vue');
}

window.moment = require('vue-moment');
if (window.moment === undefined) {
  Vue.use(window.moment);
}

if (window.qs === undefined) {
  window.qs = require('qs');
}

import Paginate from 'vuejs-paginate'
import InfiniteLoading from 'vue-infinite-loading';
Vue.use(InfiniteLoading, { /* options */ });
Vue.component('paginate', Paginate)
Vue.component('infinite-loading', InfiniteLoading)
