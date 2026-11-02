<template>
    <div class="container mx-auto mt-10 p-4">
        <h2 class="text-3xl font-bold text-center text-blue-700 mb-6">Gestión de Asociados</h2>

        <!-- Formulario para agregar/editar -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <!-- <--- Título dinámico -->
            <h3 class="text-xl font-semibold mb-4 text-gray-700">
                {{ editando ? 'Editar Asociado' : 'Agregar Nuevo Asociado' }}
            </h3>
            
            <form @submit.prevent="guardarAsociado" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <input type="text" v-model="nuevoAsociado.nombre" placeholder="Nombre" class="border p-2 rounded" required>
                <input type="text" v-model="nuevoAsociado.dpi" placeholder="DPI" class="border p-2 rounded" required>
                <input type="email" v-model="nuevoAsociado.correo" placeholder="Correo" class="border p-2 rounded">
                <input type="text" v-model="nuevoAsociado.telefono" placeholder="Teléfono" class="border p-2 rounded">
                
                <!-- <--- NUEVO: Botón dinámico (Guardar o Actualizar) -->
                <button type="submit" class="text-white font-bold p-2 rounded transition"
                        :class="editando ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-blue-600 hover:bg-blue-700'">
                    {{ editando ? 'Actualizar' : 'Guardar' }}
                </button>
                
                <!-- <--- NUEVO: Botón Cancelar (solo aparece en modo edición) -->
                <button type="button" v-if="editando" @click="cancelarEdicion" class="bg-gray-400 text-white p-2 rounded hover:bg-gray-500">
                    Cancelar
                </button>
            </form>
        </div>

                <!-- Tabla de asociados -->
        <div class="mb-4">
            <input type="text" v-model="busqueda" placeholder="Buscar por nombre o DPI..." class="border p-2 rounded w-full">
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
                    <!-- CAMBIO: asociadosFiltrados en lugar de asociados -->
                    <tr v-for="asociado in asociadosFiltrados" :key="asociado.id" class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ asociado.id }}</td>
                        <td class="p-3">{{ asociado.nombre }}</td>
                        <td class="p-3">{{ asociado.dpi }}</td>
                        <td class="p-3">{{ asociado.correo }}</td>
                        <td class="p-3">{{ asociado.telefono }}</td>
                        <td class="p-3">
                            <button @click="editarAsociado(asociado)" class="bg-yellow-500 text-white px-3 py-1 rounded mr-2 hover:bg-yellow-600">Editar</button>
                            <button @click="eliminarAsociado(asociado.id)" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Eliminar</button>
                        </td>
                    </tr>
                    <!--  cambio del v-if para que use asociadosFiltrados -->
                    <tr v-if="asociadosFiltrados.length === 0">
                        <td colspan="6" class="text-center p-4 text-gray-500">No se encontraron asociados.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'; // Agregamos computed para la búsqueda
import axios from 'axios';

const asociados = ref([]);
const nuevoAsociado = ref({ nombre: '', dpi: '', correo: '', telefono: '' });
const URL_API = 'http://127.0.0.1:8000/api/asociados';

// <--- NUEVO: Variables para el modo edición
const editando = ref(false);
const idEditando = ref(null);
const busqueda = ref('');

//Propiedad computada para filtrar asociados según la búsqueda
const asociadosFiltrados = computed(() => {
    // si la busqueda esta vacia, devolvemos todos los asociados.
    if (!busqueda.value) {
        return asociados.value;
    }
    return asociados.value.filter(asociado => 
        asociado.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
        asociado.dpi.includes(busqueda.value)
    );
});

// 1. Listar
const cargarAsociados = async () => {
    try {
        const response = await axios.get(URL_API);
        asociados.value = response.data;
    } catch (error) {
        console.error("Error al cargar:", error);
    }
};

// 2. Guardar o Actualizar
const guardarAsociado = async () => {
    try {
        if (editando.value) {
            // <--- NUEVO: Si estamos editando, usamos PUT
            await axios.put(`${URL_API}/${idEditando.value}`, nuevoAsociado.value);
        } else {
            // Si no, usamos POST normal
            await axios.post(URL_API, nuevoAsociado.value);
        }
        
        // Limpiar formulario y resetear estado
        nuevoAsociado.value = { nombre: '', dpi: '', correo: '', telefono: '' };
        editando.value = false;
        idEditando.value = null;
        cargarAsociados(); 
    } catch (error) {
        alert("Error al guardar. Revisa que el DPI no esté repetido.");
        console.error(error);
    }
};

// <--- NUEVO: Función para llenar el formulario al dar clic en Editar
const editarAsociado = (asociado) => {
    nuevoAsociado.value = { 
        nombre: asociado.nombre, 
        dpi: asociado.dpi, 
        correo: asociado.correo, 
        telefono: asociado.telefono 
    };
    editando.value = true;
    idEditando.value = asociado.id;
};

// <--- NUEVO: Función para cancelar la edición
const cancelarEdicion = () => {
    nuevoAsociado.value = { nombre: '', dpi: '', correo: '', telefono: '' };
    editando.value = false;
    idEditando.value = null;
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