<template>
  <Navbar />
  <div class="flex flex-col h-screen bg-gray-50 overflow-hidden">
    <div class="p-4 bg-white border-b shadow-sm flex justify-between items-center no-print">
      <div class="flex items-center space-x-6">
        <h1 class="text-xl font-bold text-gray-800">Generador de Carnets</h1>
      </div>
      <div class="flex space-x-2">
        <button @click="saveCarnet" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-bold">Guardar</button>
        <button @click="printCarnet" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-bold">Imprimir</button>
      </div>
    </div>
    
    <div class="flex flex-1 overflow-hidden">
      <div class="w-1/3 overflow-y-auto border-r bg-white p-6 no-print">
        <CarnetForm :form="carnetData" />
      </div>
      <div class="w-2/3 overflow-y-auto bg-gray-200 p-8 flex justify-center">
        <div id="carnet-printable">
          <CarnetPreview :data="carnetData" />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import Navbar from '@/components/sirpef/navbar.vue';
import CarnetForm from '../components/CarnetForm.vue';
import CarnetPreview from '../components/CarnetPreview.vue';
import { alerta } from '@/utils/alert';

const carnetData = ref({
  cedula: '',
  solicitante: '',
  fecha_vencimiento: ''
});

const printCarnet = () => {
  window.print();
};

const saveCarnet = async () => {
  alerta('Éxito', 'Carnet guardado exitosamente', 'success');
};
</script>
