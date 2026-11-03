import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/Login.vue';
import Asociados from './components/Asociados.vue';
import Register from './components/Register.vue'; 
import Perfil from './components/Perfil.vue'; 

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    { path: '/register', component: Register }, // <-- 2. Agregar la ruta
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
    },
    { 
        path: '/perfil', 
        component: Perfil,
        // Guardia: Si no hay token, lo manda al login
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('token');
            if (token) {
                next(); // Tiene token, adelante
            } else {
                next('/login'); // No tiene token, pa' fuera
            }
        },
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;