<template>
  <div class="carnet-paper bg-white print:shadow-none mx-auto p-4 text-black text-[10pt] font-arial leading-snug w-[8.5cm] h-[5.4cm] border rounded-lg relative flex flex-col shadow-md overflow-hidden">
    <!-- Carnet layout implementation -->
    <div class="flex h-full">
      <div class="w-1/3 flex flex-col items-center justify-center border-r p-1">
        <div class="w-16 h-20 bg-gray-200 border border-gray-300 flex items-center justify-center text-[8px] text-gray-400">FOTO</div>
      </div>
      <div class="w-2/3 p-2 flex flex-col justify-between">
        <div>
          <div class="text-[10px] font-bold uppercase text-blue-800">CÉDULA DE IDENTIDAD</div>
          <div class="text-sm font-bold">{{ data.cedula || 'V-00.000.000' }}</div>
          <div class="mt-2 text-[10px] font-bold uppercase text-blue-800">NOMBRES Y APELLIDOS</div>
          <div class="text-xs font-bold leading-tight uppercase">{{ data.solicitante || 'NOMBRE APELLIDO' }}</div>
        </div>
        <div class="text-[8px] text-gray-500 italic">Válido hasta: {{ data.fecha_vencimiento || '00/00/0000' }}</div>
      </div>
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
