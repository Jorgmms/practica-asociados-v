import { createApp } from 'vue';
import router from './index.js';
import App from './components/App.vue';

// Creamos la app usando App.vue como base
const app = createApp(App); 

// Activamos el router
app.use(router); 

// Montamos la app en el div con id="app"
app.mount('#app');
