<template>
  <div v-if="data" class="flex flex-col items-center">
    <div class="mb-4 flex items-center gap-4 bg-gray-100 p-2 rounded-lg border print:hidden">
      <span class="text-xs font-bold text-gray-600 uppercase pl-2">Zoom Vista:</span>
      <input 
        type="range" 
        v-model="zoom" 
        min="0.5" 
        max="2" 
        step="0.1" 
        class="w-32 h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer"
      />
      <span class="text-xs font-mono w-10 text-center">{{ Math.round(zoom * 100) }}%</span>
      <button @click="zoom = 1" class="text-[10px] bg-white px-2 py-1 border rounded shadow-sm hover:bg-gray-50">Reset</button>
    </div>

    <div 
      class="flex flex-row flex-wrap justify-center gap-8 print:gap-0 transition-transform duration-200 origin-top print-area print:block"
      :style="{ transform: `scale(${zoom})`, marginBottom: zoom > 1 ? `${(zoom - 1) * 8.57}cm` : '0' }"
    >
      <div class="carnet-paper bg-white print:shadow-none text-black text-[10pt] font-arial leading-snug w-[5.4cm] h-[8.57cm] border rounded-lg relative flex flex-col shadow-md overflow-hidden">
        <div v-if="data && data.bg_img" class="absolute top-0 left-0 w-full h-[45%] z-0 px-1">
          <img :src="data.bg_img" class="w-full h-full object-cover" />
        </div>

        <div class="relative z-10 flex flex-col items-center h-full">
          <div class="mt-[85px] mb-2">
            <div v-if="data && data.foto_img" class="w-[82px] h-[90px] rounded-2xl shadow-xl overflow-hidden bg-white border border-gray-100 mt-4">
              <img :src="data.foto_img" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-[82px] h-[90px] bg-gray-200 rounded-2xl shadow-xl flex items-center justify-center text-[10px] text-gray-400">FOTO</div>
          </div>

          <div class="text-center w-full px-4 mb-4">
            <div class="text-base font-black text-[#1e3a8a] uppercase leading-tight tracking-tighter">
              {{ data.solicitante || 'NOMBRE APELLIDO' }}
            </div>
            <div class="text-[9px] font-bold text-gray-500">
              C.I. {{ data.cedula || 'V-00.000.000' }}
            </div>
            <div class="text-[10px] font-bold text-gray-600 leading-none">
              {{ data.cargo || 'Cargo que ostenta' }}
            </div>
            <div class="text-[9px] font-bold text-gray-600 leading-none mt-1">
              {{ data.oficina || 'Oficina / Unidad' }}
            </div>
          </div>

          <div class="absolute bottom-0 left-0 w-full px-2">
            <div v-if="data.footer_img">
              <img :src="data.footer_img" class="w-full h-auto max-h-12 object-contain" />
            </div>
            <div v-else class="w-full h-10 border-t border-dashed flex items-center justify-center text-[8px] text-gray-300 italic uppercase">
              Logo Institucional
            </div>
          </div>
        </div>
      </div>

      <div class="carnet-paper bg-white print:shadow-none text-black font-arial w-[5.4cm] h-[8.57cm] border rounded-lg relative flex flex-col shadow-md overflow-hidden justify-between">
        
        <div v-if="data && data.reverso_bg_img" class="absolute top-0 left-0 w-full h-[45%] z-0">
          <img :src="data.reverso_bg_img" class="w-full object-cover h-[60%]" />
        </div>

        <div class="w-full flex justify-end items-center h-[1.3cm] pr-2 pt-1 relative z-10">
          <div v-if="!data.reverso_bg_img" class="text-[6px] text-gray-400 font-bold uppercase">Logo República</div>
        </div>

        <div class="w-full flex-1 px-3 overflow-hidden mt-1 relative block z-10">
          
          <div class="float-right ml-1 mb-1 mt-36 w-[32%] flex flex-col items-center">
            <div class="w-[1.3cm] h-[1.3cm] border border-black p-[2px] rounded-sm bg-white flex items-center justify-center">
              <img v-if="data.reverso_qr_img" :src="data.reverso_qr_img" class="w-full h-full object-contain" />
              <div v-else class="w-full h-full bg-gray-50 flex items-center justify-center text-[5px] text-center font-bold font-mono uppercase">QR</div>
            </div>
           
          </div>

          <div class="text-gray-900 leading-[1.1] text-[6px] text-left tracking-tighter whitespace-pre-line mt-10">
            {{ data.reverso_texto_superior }}
          </div>

        </div>

        <div class="w-full h-[1.6cm] flex items-end justify-between px-1 pb-1">
          <div class="w-[1.3cm] h-[1.3cm] flex items-center justify-center ml-1">
            <img v-if="data.reverso_sello_img" :src="data.reverso_sello_img" class="w-full h-full object-contain" />
            <div v-else class="w-full h-full rounded-full border border-dashed border-gray-400 flex items-center justify-center text-[5px] text-gray-400 font-bold">SELLO</div>
          </div>

          <div class="flex flex-col items-center mr-1">
            <div class="w-[1.9cm] h-[0.9cm] bg-gray-200/60 border border-gray-300 rounded flex items-center justify-center overflow-hidden">
              <img v-if="data.reverso_firma_img" :src="data.reverso_firma_img" class="w-full h-full object-contain" />
              <span v-else class="text-[7px] font-black text-gray-500 uppercase tracking-wider font-mono">FIRMA</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import Http from '@/utils/Http';

const zoom = ref(1.5);

const props = defineProps({
  data: {
    type: Object,
    required: true,
    default: () => ({})
  }
});

const emit = defineEmits(['saved']);

const fetchLastActiveConfig = async () => {
  try {
    const response = await Http.get('/api/registro/carnets');
    const configs = response.data;
    const lastActive = configs.filter((c: any) => c.estatus).pop();
    
    if (lastActive) {
      if (!props.data.reverso_texto_superior) props.data.reverso_texto_superior = lastActive.texto_superior;
      if (!props.data.reverso_texto_inferior) props.data.reverso_texto_inferior = lastActive.texto_inferior;
      if (!props.data.reverso_sello_img) props.data.reverso_sello_img = lastActive.sello;
      if (!props.data.reverso_firma_img) props.data.reverso_firma_img = lastActive.firma;
      if (!props.data.bg_img) props.data.bg_img = lastActive.imagen_fondo;
      if (!props.data.footer_img) props.data.footer_img = lastActive.imagen_pie_pagina;
    }
  } catch (error) {
    console.error("Error al cargar previsualización activa:", error);
  }
};

onMounted(fetchLastActiveConfig);

const toDataURL = async (url: string): Promise<string> => {
  if (!url) throw new Error('No URL provided');
  if (url.startsWith('data:')) return url;
  
  const response = await fetch(url, { cache: 'no-cache' });
  if (!response.ok) throw new Error(`Failed to fetch image: ${response.statusText}`);
  
  const blob = await response.blob();
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onloadend = () => resolve(reader.result as string);
    reader.onerror = reject;
    reader.readAsDataURL(blob);
  });
};
</script>

<style scoped>
.carnet-paper {
  font-family: 'Arial Black', Arial, Helvetica, sans-serif !important;
}

@media print {
  @page {
    size: 5.4cm 8.57cm;
    margin: 0;
  }
  body > *:not(.print-area) {
    display: none !important;
  }
  body {
    visibility: hidden;
    margin: 0 !important;
    padding: 0 !important;
  }
  .print-area, .print-area * {
    visibility: visible;
  }
  .print-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 5.4cm;
    display: block !important;
    background: white !important;
    z-index: 9999;
    margin: 0 !important;
    padding: 0 !important;
  }
  .origin-top {
    transform: none !important;
    margin-bottom: 0 !important;
    display: block !important;
  }
  .carnet-paper {
    box-shadow: none !important;
    border: none !important;
    margin: 0 !important;
    page-break-after: always !important;
    break-after: page !important;
    display: flex !important;
  }
  .carnet-paper:nth-child(n+3) {
    display: none !important;
  }
  .carnet-paper:nth-child(2) {
    page-break-after: avoid !important;
    break-after: avoid !important;
    display: flex !important;
  }
  .print-area {
    transform: none !important;
    margin-bottom: 0 !important;
  }
}
</style>
