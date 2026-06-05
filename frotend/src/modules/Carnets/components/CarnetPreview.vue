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
      
      <button 
        @click="downloadPDF" 
        class="ml-auto flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-[10px] font-bold uppercase transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Descargar PDF
      </button>
    </div>

    <div 
      class="flex flex-row flex-wrap justify-center gap-8 print:gap-0 transition-transform duration-200 origin-top print-area print:block"
      :style="{ transform: `scale(${zoom})`, marginBottom: zoom > 1 ? `${(zoom - 1) * 8.57}cm` : '0' }"
    >
      <!-- ANVERSO -->
    <div class="carnet-paper bg-white print:shadow-none text-black text-[10pt] font-arial leading-snug w-[5.4cm] h-[8.57cm] border rounded-lg relative flex flex-col shadow-md overflow-hidden">
      <!-- Background Image (Top Half) -->
      <div v-if="data && data.bg_img" class="absolute -top-4 left-0 w-full h-[55%] z-0">
        <img :src="data.bg_img" class="w-full h-full object-cover" />
      </div>

      <!-- Content Container -->
      <div class="relative z-10 flex flex-col items-center h-full">
        <!-- Photo centered -->
        <div class="mt-24 mb-6">
          <div v-if="data && data.foto_img" class="w-[82px] h-[90px] rounded-2xl shadow-xl overflow-hidden bg-white">
            <img :src="data.foto_img" class="w-full h-full object-cover" />
          </div>
          <div v-else class="w-[82px] h-[90px] bg-gray-200 rounded-2xl shadow-xl flex items-center justify-center text-[10px] text-gray-400">FOTO</div>
        </div>

        <!-- Data below photo -->
        <div class="text-center w-full px-4 mb-4">
          <div class="text-xl font-black text-[#1e3a8a] uppercase leading-tight tracking-tighter mb-1">
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
        <div class="mt-auto w-full px-4 pb-8">
          <div v-if="data.footer_img">
            <img :src="data.footer_img" class="w-full h-auto max-h-16 object-contain" />
          </div>
          <div v-else class="w-full h-16 border-t flex items-center justify-center text-[8px] text-gray-300 italic uppercase">
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

const downloadPDF = async () => {
  const doc = new jsPDF({
    orientation: 'portrait',
    unit: 'mm',
    format: [54, 85.7]
  });

  const width = 54;
  const height = 85.7;

  // --- ANVERSO ---
  if (props.data.bg_img) {
    try {
      const bgData = props.data.bg_img.startsWith('data:') ? props.data.bg_img : await toDataURL(props.data.bg_img);
      doc.addImage(bgData, 'PNG', 0, 0, width, height * 0.5);
    } catch (e) { console.error("Error loading bg_img", e); }
  }

  if (props.data.foto_img) {
    try {
      const fotoData = props.data.foto_img.startsWith('data:') ? props.data.foto_img : await toDataURL(props.data.foto_img);
      doc.addImage(fotoData, 'PNG', 16, 22, 22, 24);
    } catch (e) { console.error("Error loading foto_img", e); }
  }

  doc.setTextColor(30, 58, 138);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.text((props.data.solicitante || 'NOMBRE APELLIDO').toUpperCase(), width / 2, 50, { align: 'center', maxWidth: 40, lineHeightFactor: 1.1 });

  doc.setTextColor(107, 114, 128);
  doc.setFontSize(8);
  doc.text(`C.I. ${props.data.cedula || 'V-00.000.000'}`, width / 2, 56, { align: 'center' });

  doc.setTextColor(75, 85, 99);
  doc.setFontSize(8);
  doc.text(props.data.cargo || 'Cargo que ostenta', width / 2, 61, { align: 'center', maxWidth: 42, lineHeightFactor: 1 });
  
  doc.setFontSize(7);
  doc.text(props.data.oficina || 'Oficina / Unidad', width / 2, 66, { align: 'center', maxWidth: 42, lineHeightFactor: 1 });

  if (props.data.footer_img) {
    try {
      const footerData = props.data.footer_img.startsWith('data:') ? props.data.footer_img : await toDataURL(props.data.footer_img);
      doc.addImage(footerData, 'PNG', 5, 72, width - 10, 10);
    } catch (e) { console.error("Error loading footer_img", e); }
  }

  doc.setTextColor(156, 163, 175);
  doc.setFontSize(5);
  doc.text(`Emisión: ${props.data.fecha_emision || '00/00/0000'}`, width - 5, 83, { align: 'right' });

  // --- REVERSO ---
  doc.addPage();
  
  doc.setTextColor(55, 65, 81);
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(6);
  
  const splitTop = doc.splitTextToSize(props.data.reverso_texto_superior || 'Información institucional superior.', width - 10);
  doc.text(splitTop, 5, 8);

  if (props.data.reverso_sello_img) {
    try {
      const selloData = props.data.reverso_sello_img.startsWith('data:') ? props.data.reverso_sello_img : await toDataURL(props.data.reverso_sello_img);
      doc.addImage(selloData, 'PNG', (width - 15) / 2, 30, 15, 15);
    } catch (e) { console.error("Error loading sello_img", e); }
  }
  if (props.data.reverso_firma_img) {
    try {
      const firmaData = props.data.reverso_firma_img.startsWith('data:') ? props.data.reverso_firma_img : await toDataURL(props.data.reverso_firma_img);
      doc.addImage(firmaData, 'PNG', (width - 25) / 2, 48, 25, 10);
    } catch (e) { console.error("Error loading firma_img", e); }
  }

  const splitBottom = doc.splitTextToSize(props.data.reverso_texto_inferior || 'Este carnet es personal e intransferible.', width - 10);
  doc.text(splitBottom, 5, 75);

  window.open(doc.output('bloburl'), '_blank');
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
