<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center text-blue-700 mb-6">Iniciar Sesión</h2>
            
            <!-- Mensaje de error -->
            <div v-if="error" class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                {{ error }}
            </div>

            <form @submit.prevent="handleLogin" autocomplete="off">
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Correo Electrónico</label>
        <input 
            type="email" 
            v-model="email" 
            class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
            placeholder="admin@test.com" 
            required
            autocomplete="off" 
        >
    </div>

    <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2">Contraseñaa</label>
        <input 
            type="password" 
            v-model="password" 
            class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
            placeholder="••••••••" 
            required
            autocomplete="new-password" 
        >
    </div>

    <button type="submit" :disabled="cargando" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-300">
        {{ cargando ? 'Ingresando...' : 'Ingresar' }}
    </button>
</form>
<div class="mt-4 text-center text-sm text-gray-600">
    ¿No tienes cuenta? 
    <router-link to="/register" class="text-green-600 hover:underline">Regístrate aquí</router-link>
</div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router'; // Importamos para redirigir

const email = ref(''); // Dejamos el correo por defecto para hacer pruebas rápido
const password = ref('');      // Y la contraseña
const error = ref('');
const cargando = ref(false);
const router = useRouter(); // Herramienta para cambiar de pantalla

const handleLogin = async () => {
    cargando.value = true;
    error.value = '';

    try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
            email: email.value,
            password: password.value
        });

        // Guardamos el token y el rol
        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user_role', response.data.user.role);
        localStorage.setItem('user_name', response.data.user.name); // <-- NUEVA LÍNEA

        // Redirección según el rol
        if (response.data.user.role === 'admin') {
            router.push('/asociados');
        } else {
            router.push('/perfil');
        }

        // Redirección según el rol
        if (response.data.user.role === 'admin') {
            router.push('/asociados');
        } else {
            router.push('/perfil'); // <-- Si es asociado, va a su perfil
        }

    } catch (err) {
        if (err.response && err.response.status === 401) {
            error.value = 'Credenciales incorrectas. Verifica tu correo y contraseña.';
        } else {
            error.value = 'Error de conexión con el servidor.';
        }
    } finally {
        cargando.value = false;
    }
};
</script>