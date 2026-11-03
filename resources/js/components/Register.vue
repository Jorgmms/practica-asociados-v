<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center text-green-700 mb-6">Crear Nueva Cuenta</h2>
            
            <!-- Mensaje de error si el correo ya existe o hay fallos -->
            <div v-if="error" class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                {{ error }}
            </div>

            <form @submit.prevent="handleRegister" autocomplete="off">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nombre Completo</label>
                    <input type="text" v-model="name" class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Ej. Pedro Pérez" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Correo Electrónico</label>
                    <input type="email" v-model="email" class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="correo@ejemplo.com" required>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña (mín. 6 caracteres)</label>
                    <input type="password" v-model="password" class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="••••••••" required minlength="6">
                </div>

                <button type="submit" :disabled="cargando" class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition duration-300">
                    {{ cargando ? 'Registrando...' : 'Registrarse' }}
                </button>
            </form>

            <!-- Enlace para volver al Login -->
            <div class="mt-4 text-center text-sm text-gray-600">
                ¿Ya tienes cuenta? 
                <router-link to="/login" class="text-blue-600 hover:underline font-bold">Inicia Sesión aquí</router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const name = ref('');
const email = ref('');
const password = ref('');
const error = ref('');
const cargando = ref(false);
const router = useRouter();

const handleRegister = async () => {
    // ... (validaciones)
    try {
        const response = await axios.post('http://127.0.0.1:8000/api/register', {
            name: name.value,
            email: email.value,
            password: password.value
        });

        // Guardamos el token y el rol
        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user_role', response.data.user.role);
        
        // Guardamos el nombre para mostrarlo en el perfil
        localStorage.setItem('user_name', response.data.user.name);

        // Redirigimos al perfil de asociado
        router.push('/perfil');

    } catch (err) {
        // ... (errores)
    }
};
</script>