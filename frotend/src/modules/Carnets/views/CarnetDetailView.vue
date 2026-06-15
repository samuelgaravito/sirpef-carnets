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

    <div v-else-if="carnetData" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Left: Form -->
      <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-inner">
        <CarnetForm :form="carnetData" />
      </div>

      <!-- Right: Preview -->
      <div class="flex flex-col items-center bg-gray-100 p-6 rounded-xl border border-gray-200">
        <h3 class="text-lg font-bold text-gray-700 mb-6 uppercase tracking-wider border-b-2 border-blue-500 pb-2">Vista Previa Real</h3>
        <CarnetPreview :data="carnetData" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Navbar from "@/components/sirpef/navbar.vue";
import CarnetPreview from "../components/CarnetPreview.vue";
import CarnetForm from "../components/CarnetForm.vue";
import Http from "@/utils/Http";

const route = useRoute();
const router = useRouter();
const carnetData = ref(null);
const loading = ref(true);

const fetchCarnetDetail = async () => {
  try {
    const { id } = route.params;
    const response = await Http.get(`/carnets/registros/${id}`);
    const reg = response.data;
    
    // Map the API response to the format expected by CarnetPreview and CarnetForm
    carnetData.value = {
      solicitante: reg.solicitante,
      cedula: reg.cedula,
      cargo: reg.cargo,
      oficina: reg.oficina,
      foto_img: reg.foto_img,
      reverso_sello_img: reg.info_carnet?.sello,
      reverso_firma_img: reg.info_carnet?.firma,
      bg_img: reg.info_carnet?.imagen_fondo,
      footer_img: reg.info_carnet?.imagen_pie_pagina,
      reverso_bg_img: reg.info_carnet?.imagen_fondo_reverso,
      reverso_qr_img: reg.info_carnet?.qr_reverso
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
