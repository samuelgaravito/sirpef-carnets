<template>
  <div v-if="data" class="flex flex-col items-center">
    <!-- Zoom Controls -->
    <div class="mb-4 flex items-center gap-4 bg-gray-100 p-2 rounded-lg border print:hidden">
      <span class="text-xs font-bold text-gray-600 uppercase">Zoom Vista:</span>
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
      <!-- ANVERSO -->
    <div class="carnet-paper bg-white print:shadow-none text-black text-[10pt] font-arial leading-snug w-[5.4cm] h-[8.57cm] border rounded-lg relative flex flex-col shadow-md overflow-hidden">
      <!-- Background Image (Top Half) -->
      <div v-if="data && data.bg_img" class="absolute top-0 left-0 w-full h-[50%] z-0">
        <img :src="data.bg_img" class="w-full h-full object-cover" />
      </div>

      <!-- Content Container -->
      <div class="relative z-10 flex flex-col items-center h-full">
        <!-- Photo centered -->
        <div class="mt-[85px] mb-4">
          <div v-if="data && data.foto_img" class="w-[82px] h-[90px] rounded-2xl shadow-xl overflow-hidden bg-white border border-gray-100">
            <img :src="data.foto_img" class="w-full h-full object-cover" />
          </div>
          <div v-else class="w-[82px] h-[90px] bg-gray-200 rounded-2xl shadow-xl flex items-center justify-center text-[10px] text-gray-400">FOTO</div>
        </div>

        <!-- Data below photo -->
        <div class="text-center w-full px-4 mb-4">
          <div class="text-base font-black text-[#1e3a8a] uppercase leading-tight tracking-tighter mb-1">
            {{ data.solicitante || 'NOMBRE APELLIDO' }}
          </div>
          <div class="text-[9px] font-bold text-gray-500 mt-1">
            C.I. {{ data.cedula || 'V-00.000.000' }}
          </div>
          <div class="text-[10px] font-bold text-gray-600 leading-none mt-1">
            {{ data.cargo || 'Cargo que ostenta' }}
          </div>
          <div class="text-[9px] font-bold text-gray-600 leading-none mt-1">
            {{ data.oficina || 'Oficina / Unidad' }}
          </div>
        </div>

        <!-- Footer Logo Area -->
        <div class="absolute bottom-0 left-0 w-full px-2">
          <div v-if="data.footer_img">
            <img :src="data.footer_img" class="w-full h-auto max-h-12 object-contain" />
          </div>
          <div v-else class="w-full h-10 border-t border-dashed flex items-center justify-center text-[8px] text-gray-300 italic uppercase">
            Logo Institucional
          </div>
        </div>
        
        <div class="absolute bottom-1 right-2 text-[6px] text-gray-400 font-bold">
          Emisión: {{ data.fecha_emision || '00/00/0000' }}
        </div>
      </div>
    </div>

    <!-- REVERSO -->
    <div class="carnet-paper bg-white print:shadow-none text-black text-[10pt] font-arial leading-snug w-[5.4cm] h-[8.57cm] border rounded-lg relative flex flex-col shadow-md overflow-hidden p-4">
      
      <!-- Top Paragraph -->
      <div class="relative z-10 text-[7px] text-justify leading-tight text-gray-700 mb-auto whitespace-pre-wrap">
        {{ data.reverso_texto_superior || 'Información institucional superior del reverso.' }}
      </div>

      <!-- Middle Content (Sello and Firma) -->
      <div class="relative z-10 flex flex-col items-center justify-center gap-4 flex-1">
        <div v-if="data.reverso_sello_img" class="w-14 h-auto">
          <img :src="data.reverso_sello_img" class="w-full h-auto object-contain" />
        </div>
        
        <div v-if="data.reverso_firma_img" class="flex flex-col items-center w-full">
          <img :src="data.reverso_firma_img" class="w-20 h-auto" />
        </div>
      </div>
      
      <!-- Bottom Paragraph -->
      <div class="relative z-10 text-[7px] text-justify leading-tight text-gray-700 mt-auto whitespace-pre-wrap">
        {{ data.reverso_texto_inferior || 'Este carnet es personal e intransferible. Su uso indebido será sancionado. En caso de extravío favor devolverlo a la oficina de recursos humanos.' }}
      </div>
    </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import Http from '@/utils/Http';
import { jsPDF } from 'jspdf';
import html2canvas from 'html2canvas';

const zoom = ref(1);

const props = defineProps({
  data: {
    type: Object,
    required: true,
    default: () => ({})
  }
});

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
  /* Ensure only the first two elements print if more exist */
  .carnet-paper:nth-child(n+3) {
    display: none !important;
  }
  /* Ensure the second element (reverso) starts on a new page and is the last one printed */
  .carnet-paper:nth-child(2) {
    page-break-after: avoid !important;
    break-after: avoid !important;
    display: flex !important;
  }
  /* Reset scaling for print */
  .print-area {
    transform: none !important;
    margin-bottom: 0 !important;
  }
}
</style>
