<template>
  <div v-if="data" class="carnet-paper bg-white print:shadow-none mx-auto text-black text-[10pt] font-arial leading-snug w-[5.4cm] h-[8.5cm] border rounded-lg relative flex flex-col shadow-md overflow-hidden">
    <!-- Background Image (Top Half) -->
    <div v-if="data && data.bg_img" class="absolute top-0 left-0 w-full h-1/2 z-0">
      <img :src="data.bg_img" class="w-full h-full object-cover" />
    </div>

    <!-- Content Container -->
    <div class="relative z-10 flex flex-col items-center h-full p-4">
      <!-- Photo centered -->
      <div class="mt-8 mb-4">
        <div v-if="data && data.foto_img" class="w-24 h-32 border-2 border-white shadow-lg overflow-hidden bg-white">
          <img :src="data.foto_img" class="w-full h-full object-cover" />
        </div>
        <div v-else class="w-24 h-32 bg-gray-200 border-2 border-white shadow-lg flex items-center justify-center text-[10px] text-gray-400">FOTO</div>
      </div>

      <!-- Data below photo -->
      <div class="text-center w-full space-y-2">
        <div>
          <div class="text-[9px] font-bold uppercase text-blue-800 leading-none">CÉDULA DE IDENTIDAD</div>
          <div class="text-sm font-bold">{{ data.cedula || 'V-00.000.000' }}</div>
        </div>
        <div>
          <div class="text-[9px] font-bold uppercase text-blue-800 leading-none">NOMBRES Y APELLIDOS</div>
          <div class="text-xs font-bold leading-tight uppercase px-2">{{ data.solicitante || 'NOMBRE COMPLETO' }}</div>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-auto text-[8px] text-gray-500 italic">Válido hasta: {{ data.fecha_vencimiento || '00/00/0000' }}</div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  data: Object
});
</script>

<style scoped>
.carnet-paper {
  font-family: Arial, Helvetica, sans-serif !important;
}
@media print {
  .carnet-paper {
    box-shadow: none !important;
    border: 1px solid #ccc !important;
  }
}
</style>
