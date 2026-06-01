<template>
  <div v-if="data" class="flex flex-col gap-8 print:gap-0">
    <!-- ANVERSO -->
    <div class="carnet-paper bg-white print:shadow-none mx-auto text-black text-[10pt] font-arial leading-snug w-[216px] h-[334px] border rounded-lg relative flex flex-col shadow-md overflow-hidden">
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
          Vence: {{ data.fecha_vencimiento || '00/00/0000' }}
        </div>
      </div>
    </div>

    <!-- REVERSO -->
    <div class="carnet-paper bg-white print:shadow-none mx-auto text-black text-[10pt] font-arial leading-snug w-[216px] h-[334px] border rounded-lg relative flex flex-col shadow-md overflow-hidden print:mt-4 p-4">
      
      <!-- Top Paragraph -->
      <div class="relative z-10 text-[7px] text-justify leading-tight text-gray-700 mb-auto">
        {{ data.reverso_texto_superior || 'Información institucional superior del reverso.' }}
      </div>

      <!-- Middle Content (Sello and Firma) -->
      <div class="relative z-10 flex flex-col items-center justify-center gap-4 flex-1">
        <div v-if="data.reverso_sello_img" class="w-14 h-auto">
          <img :src="data.reverso_sello_img" class="w-full h-auto object-contain" />
        </div>
        
        <div v-if="data.reverso_firma_img" class="flex flex-col items-center">
          <img :src="data.reverso_firma_img" class="w-32 h-auto" />
        </div>
      </div>
      
      <!-- Bottom Paragraph -->
      <div class="relative z-10 text-[7px] text-justify leading-tight text-gray-700 mt-auto">
        {{ data.reverso_texto_inferior || 'Este carnet es personal e intransferible. Su uso indebido será sancionado. En caso de extravío favor devolverlo a la oficina de recursos humanos.' }}
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
const props = defineProps({
  data: {
    type: Object,
    required: true,
    default: () => ({})
  }
});
</script>

<style scoped>
.carnet-paper {
  font-family: 'Arial Black', Arial, Helvetica, sans-serif !important;
}
@media print {
  .carnet-paper {
    box-shadow: none !important;
    border: 1px solid #ccc !important;
  }
}
</style>
