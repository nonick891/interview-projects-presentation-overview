import './assets/main.css';

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import '@mdi/font/css/materialdesignicons.css';
import 'vuetify/styles';
import { createVuetify } from 'vuetify';

const app = createApp(App);
const vuetify = createVuetify();

app.use(router);
app.use(vuetify);

app.mount('#app');
