<template>
    <div class="container mx-auto mt-10 p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-blue-700">Gestión de Asociados</h2>
            <button @click="cerrarSesion" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Cerrar Sesión</button>
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

        <!-- ========================================== -->
        <!-- VENTANA MODAL (POP-UP) PARA EDITAR         -->
        <!-- ========================================== -->
        <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
                <h3 class="text-2xl font-bold mb-4 text-gray-800">Editar Asociado</h3>
                
                <form @submit.prevent="guardarAsociado">
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Nombre</label>
                        <input type="text" v-model="nuevoAsociado.nombre" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 text-gray-700">DPI</label>
                        <input type="text" v-model="nuevoAsociado.dpi" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Correo</label>
                        <input type="email" v-model="nuevoAsociado.correo" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Teléfono</label>
                        <input type="text" v-model="nuevoAsociado.telefono" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="cancelarEdicion" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Fin del Modal -->

    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const asociados = ref([]);
const nuevoAsociado = ref({ nombre: '', dpi: '', correo: '', telefono: '' });
const URL_API = 'http://127.0.0.1:8000/api/asociados';
const mostrarModal = ref(false);

// Variables para edición y búsqueda
const editando = ref(false);
const idEditando = ref(null);
const busqueda = ref('');

const router = useRouter(); // 2. Inicializacion del rooter

// - CONFIGURACIÓN DE SEGURIDAD
// se creo unainstancia de axios que siempre envíe el token
const api = axios.create({
    baseURL: URL_API,
    headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
    }
});

// Propiedad computada para la búsqueda
const asociadosFiltrados = computed(() => {
    if (!busqueda.value) return asociados.value;
    return asociados.value.filter(asociado => 
        asociado.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
        asociado.dpi.includes(busqueda.value)
    );
});

// 1. Listar (Usamos 'api' en lugar de 'axios')
const cargarAsociados = async () => {
    try {
        const response = await api.get(''); // Como ya tiene la baseURL, dejamos vacío
        asociados.value = response.data;
    } catch (error) {
        console.error("Error al cargar:", error);
        if (error.response && error.response.status === 401) {
            cerrarSesion(); // Si el token expiró, lo sacamos
        }
    }
};

// 2. Guardar o Actualizar (Usamos 'api')
const guardarAsociado = async () => {
    try {
        if (editando.value) {
            await api.put(`/${idEditando.value}`, nuevoAsociado.value);
        } else {
            await api.post('', nuevoAsociado.value);
        }
        
        nuevoAsociado.value = { nombre: '', dpi: '', correo: '', telefono: '' };
        editando.value = false;
        idEditando.value = null;
        cargarAsociados(); 
    } catch (error) {
        alert("Error al guardar. Revisa que el DPI no esté repetido.");
        console.error(error);
    }
};

// 3. Editar (Usamos 'api')
const editarAsociado = (asociado) => {
    nuevoAsociado.value = { 
        nombre: asociado.nombre, 
        dpi: asociado.dpi, 
        correo: asociado.correo, 
        telefono: asociado.telefono 
    };
    editando.value = true;
    idEditando.value = asociado.id;
    mostrarModal.value = true; // <-- Abre el modal
};
// 4. Cancelar Edición
const cancelarEdicion = () => {
    nuevoAsociado.value = { nombre: '', dpi: '', correo: '', telefono: '' };
    editando.value = false;
    idEditando.value = null;
    mostrarModal.value = false; // <-- Cierra el modal
};


// 5. Eliminar (Usamos 'api')
const eliminarAsociado = async (id) => {
    if (confirm('¿Seguro que deseas eliminar este asociado?')) {
        try {
            await api.delete(`/${id}`);
            cargarAsociados();
        } catch (error) {
            console.error("Error al eliminar:", error);
        }
    }
};

// 6. Cerrar Sesión
const cerrarSesion = () => {
    localStorage.removeItem('token');
    router.push('/login');
};

onMounted(() => {
    // Verificamos que haya token antes de cargar
    if (!localStorage.getItem('token')) {
        router.push('/login');
    } else {
        cargarAsociados();
    }
});
</script>