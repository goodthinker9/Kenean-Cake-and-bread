import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import './style.css';
import App from './App.vue';
import { appConfig } from './config';

document.title = appConfig.brandName;
const app = createApp(App);
app.use(createPinia());
app.use(router);
app.mount('#app');
