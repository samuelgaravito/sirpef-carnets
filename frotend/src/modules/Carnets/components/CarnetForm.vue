<template>
  <div class="space-y-4">
    <!-- Tab Navigation -->
    <div class="flex border-b border-gray-200">
      <button 
        @click="activeTab = 'info'"
        :class="['px-4 py-2 text-xs font-bold uppercase transition-colors', activeTab === 'info' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700']"
      >
        Información
      </button>
      <button 
        @click="activeTab = 'config'"
        :class="['px-4 py-2 text-xs font-bold uppercase transition-colors', activeTab === 'config' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700']"
      >
        Configuración
      </button>
    </div>

    <div v-if="activeTab === 'info'" class="space-y-6">
      <!-- Datos Frontal -->
      <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
        <h3 class="text-sm font-bold text-blue-800 border-b pb-1 mb-3 uppercase">Datos Frontal</h3>
        <div class="space-y-3">
          <div class="p-3 bg-white rounded border mb-4">
            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Foto del Carnet</label>
            <input type="file" @change="handleImage($event, 'foto')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
          </div>
          <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-500">NOMBRE COMPLETO</label>
            <input v-model="form.solicitante" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
          </div>
          <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-500">CÉDULA</label>
            <input v-model="form.cedula" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
          </div>
          <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-500">CARGO</label>
            <input v-model="form.cargo" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
          </div>
          <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-500">OFICINA</label>
            <input v-model="form.oficina" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
          </div>
          <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-500">FECHA DE EMISIÓN</label>
            <input v-model="form.fecha_emision" type="date" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
          </div>
        </div>
      </div>

    </div>


    <!-- Configuración Tab -->
    <div v-if="activeTab === 'config'" class="space-y-6">
      <!-- Recursos Frontal -->
      <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
        <h3 class="text-sm font-bold text-blue-800 border-b pb-1 mb-3 uppercase">Recursos Frontal</h3>
        <div class="space-y-4">
          <div class="p-3 bg-white rounded border">
            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Fondo Superior</label>
            <input type="file" @change="handleImage($event, 'bg')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
            <p class="text-[9px] text-gray-400 mt-1">Imagen que ocupará la mitad superior del carnet.</p>
          </div>

          <div class="p-3 bg-white rounded border">
            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Imagen Pie de Página</label>
            <input type="file" @change="handleImage($event, 'footer')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
            <p class="text-[9px] text-gray-400 mt-1">Logo o información institucional inferior.</p>
          </div>
        </div>
      </div>

      <!-- Datos Reverso -->
      <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
        <h3 class="text-sm font-bold text-blue-800 border-b pb-1 mb-3 uppercase">Datos Reverso</h3>
        <div class="space-y-3">
          <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-500">TEXTO SUPERIOR REVERSO</label>
            <textarea v-model="form.reverso_texto_superior" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none resize-none" rows="3"></textarea>
          </div>
          <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-500">TEXTO INFERIOR REVERSO</label>
            <textarea v-model="form.reverso_texto_inferior" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none resize-none" rows="3"></textarea>
          </div>
        </div>
      </div>

      <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
        <h3 class="text-sm font-bold text-blue-800 border-b pb-1 uppercase mb-3">Recursos Reverso</h3>
        <div class="space-y-4">
          <div class="p-3 bg-white rounded border">
            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Sello Institucional (Reverso)</label>
            <input type="file" @change="handleImage($event, 'reverso_sello')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
            <p class="text-[9px] text-gray-400 mt-1">Imagen del sello que irá en el centro del reverso.</p>
          </div>

          <div class="p-3 bg-white rounded border">
            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Firma Autorizada</label>
            <input type="file" @change="handleImage($event, 'reverso_firma')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end pt-4">
        <button 
          @click="saveConfig"
          class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase rounded shadow-md transition-colors"
        >
          Guardar Configuración
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import Http from '@/utils/Http';

const props = defineProps({
  form: Object
});

const emit = defineEmits(['save-config', 'saved']);

const activeTab = ref('info');

const saveConfig = async () => {
  try {
    const payload = {
      texto_superior: props.form.reverso_texto_superior,
      texto_inferior: props.form.reverso_texto_inferior,
      sello: props.form.reverso_sello_img,
      firma: props.form.reverso_firma_img,
      imagen_fondo: props.form.bg_img,
      imagen_pie_pagina: props.form.footer_img,
      estatus: true
    };

    const response = await Http.post('/api/registro/carnets', payload);
    alert('Configuración guardada exitosamente');
    emit('saved', response.data);
  } catch (error) {
    console.error(error);
    alert('Error al guardar la configuración: ' + (error.response?.data?.error || error.message));
  }
};

const handleImage = (event, type) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      const base64String = e.target.result;
      if (type === 'foto') props.form.foto_img = base64String;
      if (type === 'bg') props.form.bg_img = base64String;
      if (type === 'footer') props.form.footer_img = base64String;
      if (type === 'reverso_sello') props.form.reverso_sello_img = base64String;
      if (type === 'reverso_firma') props.form.reverso_firma_img = base64String;
    };
    reader.readAsDataURL(file);
  }
};
</script>
