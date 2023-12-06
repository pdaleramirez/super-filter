import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import './assets/tailwind.css'
import VueAwesomePaginate from "vue-awesome-paginate";
import "vue-awesome-paginate/dist/style.css";

const elements = document.querySelectorAll('.searchApp');
elements.forEach((element) => {
    const app = createApp(App);
    app.provide('handle', element.getAttribute('handle'));
    app.use(createPinia())
    app.use(VueAwesomePaginate);
    app.mount(element)
})
