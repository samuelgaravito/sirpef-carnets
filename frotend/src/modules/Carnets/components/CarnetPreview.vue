<template>
  <div v-if="data" class="flex flex-col items-center">
    <!-- Zoom Controls -->
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
      <!-- ANVERSO -->
    <div class="carnet-paper bg-white print:shadow-none text-black text-[10pt] font-arial leading-snug w-[5.4cm] h-[8.57cm] border rounded-lg relative flex flex-col shadow-md overflow-hidden">
      <!-- Background Image (Top Half) -->
      <div v-if="data && data.bg_img" class="absolute top-0 left-0 w-full h-[45%] z-0 px-1">
        <img :src="data.bg_img" class="w-full h-full object-cover" />
      </div>

      <!-- Content Container -->
      <div class="relative z-10 flex flex-col items-center h-full">
        <!-- Photo centered -->
        <div class="mt-[85px] mb-2">
          <div v-if="data && data.foto_img" class="w-[82px] h-[90px] rounded-2xl shadow-xl overflow-hidden bg-white border border-gray-100 mt-4">
            <img :src="data.foto_img" class="w-full h-full object-cover" />
          </div>
          <div v-else class="w-[82px] h-[90px] bg-gray-200 rounded-2xl shadow-xl flex items-center justify-center text-[10px] text-gray-400">FOTO</div>
        </div>

        <!-- Data below photo -->
        <div class="text-center w-full px-4 mb-4">
          <div class="text-base font-black text-[#1e3a8a] uppercase leading-tight tracking-tighter ">
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

        <!-- Footer Logo Area -->
        <div class="absolute bottom-0 left-0 w-full px-2">
          <div v-if="data.footer_img">
            <img :src="data.footer_img" class="w-full h-auto max-h-12 object-contain" />
          </div>
          <div v-else class="w-full h-10 border-t border-dashed flex items-center justify-center text-[8px] text-gray-300 italic uppercase">
            Logo Institucional
          </div>
        </div>
        
        <!--<div class="absolute bottom-1 right-2 text-[6px] text-gray-400 font-bold">
          Emisión: {{ data.fecha_emision || '00/00/0000' }}
        </div>-->
      </div>
    </div>

    <!-- REVERSO -->
    <div class="carnet-paper bg-white print:shadow-none text-black text-[10pt] font-arial leading-tight w-[5.4cm] h-[8.57cm] border rounded-lg relative flex flex-col shadow-md overflow-hidden">
      <!-- Header Image (from carnetdiseno.png) -->
      <div v-if="data && data.bg_img" class="w-full h-[18%] pt-2 px-1">
        <img :src="data.bg_img" class="w-full h-full object-contain" />
      </div>

      <div class="flex-1 px-4 py-2 flex flex-col relative">
        <!-- Bullet points text -->
        <div class="text-[6px] text-gray-800 leading-tight space-y-1 mb-2">
          <div class="flex gap-1"><span>•</span> <p>Este carnet es de uso exclusivo para el personal que labora en {{ data.oficina || '(nombre del ministerio)' }}.</p></div>
          <div class="flex gap-1"><span>•</span> <p>Debe ser utilizado en un lugar visible.</p></div>
          <div class="flex gap-1"><span>•</span> <p>Puede ser retenido por la Dirección General de Seguridad cuando lo requiera.</p></div>
          <div class="flex gap-1"><span>•</span> <p>Es intransferible.</p></div>
          <div class="flex gap-1"><span>•</span> <p>Se agradece a todas las autoridades Civiles y Militares prestarle la mayor colaboración posible al portador de esta credencial, dentro de las normas legales.</p></div>
          <div class="flex gap-1"><span>•</span> <p>En caso de ser transferido a otra dirección o en caso de vencimiento, debe ser entregado a la Dirección General de Seguridad.</p></div>
          <div class="flex gap-1"><span>•</span> <p>Este carnet no acredita autoridad.</p></div>
          <div class="flex gap-1"><span>•</span> <p>En caso de extravío o perdida, debe consignar en la dirección general de Seguridad la respectiva denuncia ante el organismo competente.</p></div>
        </div>

        <!-- QR Code and Label (Middle Right) -->
        <div class="absolute right-4 top-28 flex flex-col items-center">
          <div class="w-16 h-16 border border-black p-1">
            <div class="w-full h-full bg-gray-100 flex items-center justify-center text-[8px] text-center">QR CODE</div>
          </div>
          <span class="text-[7px] font-bold mt-1">Perfil digital</span>
        </div>

        <!-- Bottom Row: Sello and Firma -->
        <div class="mt-auto flex items-end justify-between pb-4">
          <!-- Sello -->
          <div v-if="data.reverso_sello_img" class="w-16 h-16 flex items-center justify-center">
            <img :src="data.reverso_sello_img" class="w-full h-full object-contain" />
          </div>
          <div v-else class="w-16 h-16 rounded-full border border-dashed border-gray-300 flex items-center justify-center text-[6px] text-gray-300">SELLO</div>

          <!-- Firma -->
          <div class="flex flex-col items-center">
             <div v-if="data.reverso_firma_img" class="w-24 h-12 flex items-center justify-center bg-gray-200/50">
               <img :src="data.reverso_firma_img" class="w-full h-full object-contain" />
             </div>
             <div v-else class="w-24 h-12 bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600 uppercase">FIRMA</div>
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
import { alerta } from '@/utils/alert';
import { jsPDF } from 'jspdf';
import html2canvas from 'html2canvas';

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
