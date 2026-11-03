<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center text-green-700 mb-2">Solicitar Afiliación</h2>
            <p class="text-center text-sm text-gray-500 mb-6">Llena tus datos y un administrador te contactará.</p>
            
            <div v-if="mensajeExito" class="bg-green-100 text-green-700 p-4 rounded mb-4 text-sm text-center">
                {{ mensajeExito }}
                <br><br>
                <router-link to="/login" class="text-blue-600 font-bold hover:underline">Volver al Login</router-link>
            </div>

            <div v-if="error" class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                {{ error }}
            </div>

            <form v-if="!mensajeExito" @submit.prevent="handleSolicitud" autocomplete="off">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nombre Completo</label>
                    <input type="text" v-model="form.nombre" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">DPI</label>
                    <input type="text" v-model="form.dpi" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Correo Electrónico</label>
                    <input type="email" v-model="form.correo" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Teléfono</label>
                    <input type="text" v-model="form.telefono" class="w-full border p-2 rounded" required>
                </div>

                <button type="submit" :disabled="cargando" class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition">
                    {{ cargando ? 'Enviando...' : 'Enviar Solicitud' }}
                </button>
            </form>

            <div v-if="!mensajeExito" class="mt-4 text-center text-sm text-gray-600">
                ¿Ya tienes cuenta? 
                <router-link to="/login" class="text-blue-600 hover:underline font-bold">Inicia Sesión</router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const form = ref({ nombre: '', dpi: '', correo: '', telefono: '' });
const error = ref('');
const mensajeExito = ref('');
const cargando = ref(false);

const handleSolicitud = async () => {
    cargando.value = true;
    error.value = '';

    try {
        const response = await axios.post('http://127.0.0.1:8000/api/solicitar-afiliacion', form.value);
        mensajeExito.value = response.data.message;
        form.value = { nombre: '', dpi: '', correo: '', telefono: '' }; // Limpiar
    } catch (err) {
        if (err.response && err.response.data.errors) {
            const mensajes = Object.values(err.response.data.errors).flat().join(' ');
            error.value = mensajes;
        } else {
            error.value = 'Error al enviar la solicitud. Intenta de nuevo.';
        }
    } finally {
        cargando.value = false;
    }
};
</script>