<template>
    <div class="container mx-auto mt-10 p-4">
        <h2 class="text-3xl font-bold text-center text-blue-700 mb-6">Gestión de Asociados</h2>

        <!-- Formulario para agregar -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-700">Agregar Nuevo Asociado</h3>
            <form @submit.prevent="guardarAsociado" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="text" v-model="nuevoAsociado.nombre" placeholder="Nombre" class="border p-2 rounded" required>
                <input type="text" v-model="nuevoAsociado.dpi" placeholder="DPI" class="border p-2 rounded" required>
                <input type="email" v-model="nuevoAsociado.correo" placeholder="Correo" class="border p-2 rounded">
                <input type="text" v-model="nuevoAsociado.telefono" placeholder="Teléfono" class="border p-2 rounded">
                <button type="submit" class="bg-blue-600 text-white font-bold p-2 rounded hover:bg-blue-700 transition">Guardar</button>
            </form>
        </div>

        <!-- Tabla de asociados -->
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
                    <tr v-for="asociado in asociados" :key="asociado.id" class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ asociado.id }}</td>
                        <td class="p-3">{{ asociado.nombre }}</td>
                        <td class="p-3">{{ asociado.dpi }}</td>
                        <td class="p-3">{{ asociado.correo }}</td>
                        <td class="p-3">{{ asociado.telefono }}</td>
                        <td class="p-3">
                            <button @click="eliminarAsociado(asociado.id)" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Eliminar</button>
                        </td>
                    </tr>
                    <tr v-if="asociados.length === 0">
                        <td colspan="6" class="text-center p-4 text-gray-500">No hay asociados registrados.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const asociados = ref([]);
const nuevoAsociado = ref({ nombre: '', dpi: '', correo: '', telefono: '' });
const URL_API = 'http://127.0.0.1:8000/api/asociados';

// 1. Listar
const cargarAsociados = async () => {
    try {
        const response = await axios.get(URL_API);
        asociados.value = response.data;
    } catch (error) {
        console.error("Error al cargar:", error);
    }
};

// 2. Guardar
const guardarAsociado = async () => {
    try {
        await axios.post(URL_API, nuevoAsociado.value);
        nuevoAsociado.value = { nombre: '', dpi: '', correo: '', telefono: '' }; // Limpiar formulario
        cargarAsociados(); // Refrescar tabla
    } catch (error) {
        alert("Error al guardar. Revisa que el DPI no esté repetido.");
        console.error(error);
    }
};

// 3. Eliminar
const eliminarAsociado = async (id) => {
    if (confirm('¿Seguro que deseas eliminar este asociado?')) {
        try {
            await axios.delete(`${URL_API}/${id}`);
            cargarAsociados();
        } catch (error) {
            console.error("Error al eliminar:", error);
        }
    }
};

onMounted(() => {
    cargarAsociados();
});
</script>