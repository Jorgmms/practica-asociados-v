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
    ¿Aún no eres asociado? 
    <router-link to="/register" class="text-green-600 hover:underline font-bold">Solicita tu afiliación aquí</router-link>
</div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router'; 

const email = ref(''); 
const password = ref('');      
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

        // Guardamos los datos en el navegador
        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user_role', response.data.user.role);
        localStorage.setItem('user_name', response.data.user.name);

        // LÓGICA DE REDIRECCIÓN (Solo una vez)
        if (response.data.must_change_password) {
            // Si el Backend dice que debe cambiarla, lo mandamos ahí
            router.push('/cambiar-password');
        } else if (response.data.user.role === 'admin') {
            // Si es admin, a la tabla
            router.push('/dashboard');
        } else {
            // Si es asociado (y ya cambió su contraseña), a su perfil
            router.push('/perfil');
        }

        } catch (err) {
        if (err.response) {
            // Si el Backend respondió con un error (401, 403, etc.)
            if (err.response.status === 401) {
                error.value = 'Credenciales incorrectas. Verifica tu correo y contraseña.';
            } else if (err.response.status === 403) {
                // Aquí atrapamos el error de contraseña expirada
                error.value = err.response.data.message; 
            } else {
                error.value = 'Error del servidor. Intenta de nuevo';
            }
        } else {
            // Si no hay respuesta (Laravel está apagado o ESET bloqueó)
            error.value = 'Error de conexión con el servidor.';
        }
    } finally {
        cargando.value = false;
    }
};
</script>