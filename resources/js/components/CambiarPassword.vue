<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center text-yellow-600 mb-4">Cambio de Contraseña Obligatorio</h2>
            <p class="text-sm text-gray-600 mb-6 text-center">Por seguridad, debes cambiar tu contraseña temporal antes de continuar.</p>
            
            <div v-if="error" class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">{{ error }}</div>

            <form @submit.prevent="cambiarPassword">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nueva Contraseña</label>
                    <input type="password" v-model="newPassword" class="w-full border p-2 rounded" required minlength="6">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña</label>
                    <input type="password" v-model="confirmPassword" class="w-full border p-2 rounded" required minlength="6">
                </div>
                <button type="submit" class="w-full bg-yellow-500 text-white font-bold py-2 rounded hover:bg-yellow-600">
                    Actualizar Contraseña
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const newPassword = ref('');
const confirmPassword = ref('');
const error = ref('');
const router = useRouter();

const cambiarPassword = async () => {
    if (newPassword.value !== confirmPassword.value) {
        error.value = 'Las contraseñas no coinciden.';
        return;
    }

    try {
        const token = localStorage.getItem('token');
        await axios.post('http://127.0.0.1:8000/api/cambiar-password', {
            password: newPassword.value
        }, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        alert('Contraseña actualizada con éxito. Por favor, inicia sesión de nuevo.');
        localStorage.removeItem('token');
        router.push('/login');
    } catch (err) {
        error.value = 'Error al cambiar la contraseña.';
    }
};
</script>