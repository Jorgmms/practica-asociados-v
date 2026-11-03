<template>
    <div class="min-h-screen bg-gray-100 p-8">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-blue-700 mb-6">Mi Perfil de Asociado</h2>
            
            <div class="bg-blue-50 p-6 rounded-lg mb-6">
    <p class="text-lg text-gray-700">
        ¡Bienvenido, <strong>{{ nombreUsuario }}</strong>!
    </p>
    <p class="text-sm text-gray-500 mt-2">
        Has iniciado sesión como <strong>Asociado</strong>.
    </p>
</div>

            <button @click="cerrarSesion" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Cerrar Sesión
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'; 
import { useRouter } from 'vue-router';

const router = useRouter();
const nombreUsuario = ref(localStorage.getItem('user_name') || 'Asociado');

const cerrarSesion = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user_role');
    localStorage.removeItem('user_name');
    router.push('/login');
};
</script>