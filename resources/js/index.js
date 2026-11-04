import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/Login.vue';
import Asociados from './components/Asociados.vue';
import Register from './components/Register.vue'; 
import Perfil from './components/Perfil.vue';
import Solicitudes from './components/Solicitudes.vue'; 
import CambiarPassword from './components/CambiarPassword.vue';
import Dashboard from './components/Dashboard.vue';

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    { path: '/register', component: Register }, 
    { 
        path: '/asociados', 
        component: Asociados,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('token');
            if (token) next(); else next('/login');
        }
    },
    { 
        path: '/perfil', 
        component: Perfil,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('token');
            if (token) next(); else next('/login');
        }
    },
    { 
        path: '/solicitudes', 
        component: Solicitudes,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('token');
            if (token) next(); else next('/login');
        }
    },
    { 
        path: '/cambiar-password', 
        component: CambiarPassword,
        beforeEnter: (to, from, next) => {
            if (localStorage.getItem('token')) next(); else next('/login');
        }
    }, // <-- ¡AQUÍ FALTABA LA COMA!
    {   
        path: '/dashboard', 
        component: Dashboard,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem('token');
            if (token) next(); else next('/login');
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;