<template>
  <Navbar />
  <div class="w-full max-w-4xl mx-auto mt-10 p-6 bg-white shadow-lg rounded-xl">
    <div class="flex justify-between items-center mb-8 border-b pb-4 print:hidden">
      <h2 class="text-2xl font-bold text-gray-800">Vista Previa de Carnet</h2>
      <div class="flex gap-4">
        <button @click="router.back()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-all">
          Volver
        </button>
        <button @click="printCarnet" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-lg transition-all flex items-center">
          <font-awesome-icon icon="print" class="mr-2" />
          Imprimir
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-10">
      <p class="text-gray-500 italic">Cargando datos del carnet...</p>
    </div>

    <div v-else-if="carnetData" class="flex justify-center">
      <CarnetPreview :data="carnetData" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Navbar from "@/components/sirpef/navbar.vue";
import CarnetPreview from "../components/CarnetPreview.vue";
import Http from "@/utils/Http";

const route = useRoute();
const router = useRouter();
const carnetData = ref(null);
const loading = ref(true);

const fetchCarnetDetail = async () => {
  try {
    const { id } = route.params;
    const response = await Http.get(`/api/registro/carnets/registros/${id}`);
    const reg = response.data;
    
    // Map the API response to the format expected by CarnetPreview
    carnetData.value = {
      solicitante: reg.solicitante,
      cedula: reg.cedula,
      cargo: reg.cargo,
      oficina: reg.oficina,
      foto_img: reg.foto_img,
      info_carnet: reg.info_carnet
    };
  } catch (error) {
    console.error("Error fetching carnet detail:", error);
  } finally {
    loading.value = false;
  }
};

const printCarnet = () => {
  window.print();
};

onMounted(fetchCarnetDetail);
</script>
