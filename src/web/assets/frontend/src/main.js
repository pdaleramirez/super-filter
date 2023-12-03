import './assets/main.css'
import { createApp, defineExpose } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import './assets/tailwind.css'

const elements = document.querySelectorAll('.searchApp');
elements.forEach((element) => {
    const app = createApp(App);
    app.provide('handle', element.getAttribute('handle'));
    app.use(createPinia())
    app.mount(element)
})
