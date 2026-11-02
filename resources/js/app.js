import { createApp } from 'vue';
import Asociados from './components/Asociados.vue';

const app = createApp({});

app.component('asociados', Asociados);

app.mount('#app');
