import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import './assets/tailwind.css'
import VueAwesomePaginate from "vue-awesome-paginate";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";

const elements = document.querySelectorAll('.searchApp');
elements.forEach((element) => {
    const app = createApp(App);
    app.component("infinite-loading", InfiniteLoading);
    app.provide('handle', element.getAttribute('handle'));
    app.provide('options', element.getAttribute('options'));
    app.provide('fieldWatch', element.getAttribute('fieldWatch'));
    app.provide('infiniteScroll', element.getAttribute('infiniteScroll'));
    app.use(createPinia())
    app.use(VueAwesomePaginate);
    app.mount(element)
})