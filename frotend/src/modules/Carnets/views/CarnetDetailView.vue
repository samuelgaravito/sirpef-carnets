<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import CarnetPreview from "../components/CarnetPreview.vue";
import Http from "@/utils/Http";

const route = useRoute();
const router = useRouter();
const carnetData = ref({
  solicitante: '',
  cedula: '',
  cargo: '',
  oficina: '',
  foto_img: null,
  reverso_sello_img: null,
  reverso_firma_img: null,
  bg_img: null,
  footer_img: null,
  reverso_bg_img: null,
  reverso_qr_img: null
});
const loading = ref(true);

const fetchCarnetDetail = async () => {
  try {
    const { id } = route.params;
    const response = await Http.get(`/api/registro/carnets/registros/${id}`);
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


<template>
  <!-- Envoltura de tipo Modal Fijo -->
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 overflow-y-auto modal-impresion">
    
    <!-- Caja Blanca del "Modal" -->
    <div class="bg-white rounded-xl shadow-2xl  w-full p-6 relative modal-caja print:p-0">
      
      <!-- Botón X para cerrar (Regresa a la lista quitando el ID de la URL) -->
      <button @click="router.push('/carnets')" class="absolute top-4 right-4 text-gray-500 hover:text-black text-2xl print:hidden">
        &times;
      </button>

      <div class="flex justify-between items-center mb-8 border-b pb-4 print:hidden">
        <h2 class="text-2xl font-bold text-gray-800">Vista Previa de Carnet</h2>
        <div class="flex gap-4">
          <!-- Al hacer clic en Volver, limpiamos la ruta regresando a la lista -->
          <button @click="router.push('/carnets')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-all">
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

      <div v-else-if="carnetData">
        

        <div class="mx-auto flex flex-col items-center bg-gray-100 p-6 rounded-xl border border-gray-200 print:bg-transparent print:p-0 print:border-none unique-print-wrapper">
          <h3 class="text-lg font-bold text-gray-700 mb-6 uppercase tracking-wider border-b-2 border-blue-500 pb-2 print:hidden">Vista Previa Real</h3>
          <CarnetPreview :data="carnetData" />
        </div>
      </div>

    </div>
  </div>
</template>

