<template>
    <div class="container mx-auto mt-10 p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-blue-700">Solicitudes de Afiliación</h2>
            <router-link to="/asociados" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Volver a Asociados
            </router-link>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="p-3">ID</th>
                        <th class="p-3">Nombre</th>
                        <th class="p-3">DPI</th>
                        <th class="p-3">Correo</th>
                        <th class="p-3">Teléfono</th>
                        <th class="p-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="solicitud in solicitudes" :key="solicitud.id" class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ solicitud.id }}</td>
                        <td class="p-3">{{ solicitud.nombre }}</td>
                        <td class="p-3">{{ solicitud.dpi }}</td>
                        <td class="p-3">{{ solicitud.correo }}</td>
                        <td class="p-3">{{ solicitud.telefono }}</td>
                        <td class="p-3">
                        
                        <button @click="aprobarSolicitud(solicitud)" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                         Aprobar y Crear Usuario
                        </button>
                        </td>
                    </tr>
                    <tr v-if="solicitudes.length === 0">
                        <td colspan="6" class="text-center p-4 text-gray-500">No hay solicitudes pendientes.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const solicitudes = ref([]);
const URL_API = 'http://127.0.0.1:8000/api/solicitudes';

// Creamos la instancia de Axios con el Token del Admin
const api = axios.create({
    baseURL: URL_API,
    headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
    }
});

const cargarSolicitudes = async () => {
    try {
        const response = await api.get('');
        solicitudes.value = response.data;
    } catch (error) {
        console.error("Error al cargar:", error);
    }
};

const aprobarSolicitud = async (solicitud) => { // <-- Recibe el objeto completo
    if (confirm('¿Aprobar solicitud? Se enviarán las credenciales al correo del asociado.')) {
        try {
            // Aquí usamos solicitud.id (no solo "id")
            const response = await api.post(`/${solicitud.id}/aprobar`); 
            
            alert('Solicitud aprobada con éxito. Revisa la terminal de Laravel para ver las credenciales.');
            cargarSolicitudes(); 
        } catch (error) {
            alert('Error al aprobar la solicitud.');
            console.error(error);
        }
    }
};

onMounted(() => {
    cargarSolicitudes();
});
</script>