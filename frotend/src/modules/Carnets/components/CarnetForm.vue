<template>
  <div class="space-y-4">
    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
      <h3 class="text-sm font-bold text-blue-800 border-b pb-1 mb-3 uppercase">Datos del Carnet</h3>
      <div class="space-y-3">
        <div class="flex flex-col">
          <label class="text-[10px] font-bold text-gray-500">CÉDULA</label>
          <input v-model="form.cedula" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
        </div>
        <div class="flex flex-col">
          <label class="text-[10px] font-bold text-gray-500">NOMBRE COMPLETO</label>
          <input v-model="form.solicitante" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
        </div>
        <div class="flex flex-col">
          <label class="text-[10px] font-bold text-gray-500">FECHA DE VENCIMIENTO</label>
          <input v-model="form.fecha_vencimiento" type="date" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
const props = defineProps({
  form: Object
});
</script>
