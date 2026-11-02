import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/Login.vue';
import Asociados from './components/Asociados.vue';

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    { 
        path: '/asociados', 
        component: Asociados,
        // Guardia: Si no hay token, lo manda al login
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('token');
            if (token) {
                next(); // Tiene token, adelante
            } else {
                next('/login'); // No tiene token, pa' fuera
            }
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;