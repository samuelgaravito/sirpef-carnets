<template>
  <Navbar class="no-print" />
  <div class="flex flex-col h-screen bg-gray-50 overflow-hidden">
    
    
    <div class="flex flex-1 overflow-hidden">
      <div class="w-1/3 overflow-y-auto border-r bg-white p-6 no-print">
        <CarnetForm :form="carnetData" />
      </div>
      <div class="w-2/3 overflow-y-auto bg-gray-200 p-8 flex justify-center">
        <div id="carnet-printable" class="print-area">
          <CarnetPreview :data="carnetData" />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import CarnetForm from '../components/CarnetForm.vue';
import CarnetPreview from '../components/CarnetPreview.vue';
import { alerta } from '@/utils/alert';

const carnetData = ref({
  cedula: '',
  solicitante: '',
  cargo: '',
  fecha_emision: '',
  foto_img: null,
  bg_img: null,
  footer_img: null,
  reverso_sello_img: null,
  reverso_firma_img: null,
  reverso_texto_superior: '',
  reverso_texto_inferior: ''
});

const printCarnet = () => {
  const printContent = document.getElementById('carnet-printable');
  if (!printContent) return;
  window.print();
};

const saveCarnet = async () => {
  alerta('Éxito', 'Carnet guardado exitosamente', 'success');
};
</script>
