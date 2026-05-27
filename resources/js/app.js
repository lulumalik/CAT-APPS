import './bootstrap';
import '../assets/favicon_io/favicon.ico';
import '../assets/favicon_io/favicon-16x16.png';
import '../assets/favicon_io/favicon-32x32.png';
import '../assets/favicon_io/apple-touch-icon.png';
import '../assets/favicon_io/android-chrome-192x192.png';
import '../assets/favicon_io/android-chrome-512x512.png';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { registerSW } from 'virtual:pwa-register';
import App from './App.vue';
import router from './router';

registerSW({ immediate: true });

const app = createApp(App);

app.use(createPinia());
app.use(router);

app.mount('#app');