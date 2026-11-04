<template>
    <div class="container mx-auto mt-10 p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-blue-700">Dashboard de Administración</h2>
            <button @click="cerrarSesion" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Cerrar Sesión
            </button>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-500 text-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold">Total Asociados</h3>
                <p class="text-4xl font-bold">{{ stats.asociados }}</p>
            </div>
            <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold">Solicitudes Pendientes</h3>
                <p class="text-4xl font-bold">{{ stats.solicitudes }}</p>
            </div>
            <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold">Usuarios del Sistema</h3>
                <p class="text-4xl font-bold">{{ stats.usuarios }}</p>
            </div>
        </div>

        <!-- Gráfico -->
       <div class="bg-white p-6 rounded-lg shadow-lg w-full md:w-2/3 mx-auto">
    <h3 class="text-xl font-bold text-gray-700 mb-4 text-center">Resumen Visual</h3>
    <!-- Usamos un canvas nativo de HTML -->
    <canvas id="myChart" style="height: 300px;"></canvas>
</div>

        <!-- Botón para ir a Asociados -->
        <div class="mt-8 text-center">
            <router-link to="/asociados" class="bg-gray-800 text-white px-6 py-2 rounded hover:bg-gray-900">
                Ir a Gestión de Asociados
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const stats = ref({ asociados: 0, solicitudes: 0, usuarios: 0 });
const chartRef = ref(null); // Referencia al canvas
let myChart = null; // Variable para guardar la instancia del gráfico

// Cargar estadísticas
const cargarStats = async () => {
    try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/dashboard-stats', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        stats.value = response.data;
        
        // Una vez que tenemos los datos, dibujamos el gráfico
        dibujarGrafico();

    } catch (error) {
        console.error("Error al cargar estadísticas:", error);
    }
};

// Función para dibujar el gráfico usando Chart.js del CDN
const dibujarGrafico = () => {
    const ctx = document.getElementById('myChart');
    if (!ctx) return;

    // Si ya existe un gráfico, lo destruimos para no duplicarlo
    if (myChart) {
        myChart.destroy();
    }

    // Usamos la variable global "Chart" que viene del CDN
    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Asociados', 'Solicitudes', 'Usuarios'],
            datasets: [{
                label: 'Cantidad Total',
                data: [stats.value.asociados, stats.value.solicitudes, stats.value.usuarios],
                backgroundColor: ['#3b82f6', '#eab308', '#22c55e'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
};

const cerrarSesion = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user_role');
    localStorage.removeItem('user_name');
    router.push('/login');
};

onMounted(() => {
    cargarStats();
});
</script>