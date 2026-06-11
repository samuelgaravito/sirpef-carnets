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
            <select 
              v-model="form.oficina" 
              class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none"
            >
              <option value="" disabled>Seleccione una oficina...</option>
              <option v-for="m in ministerios" :key="m.id" :value="m.nombre">
                {{ m.nombre }}
              </option>
            </select>
          </div>
         <!-- <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-500">FECHA DE EMISIÓN</label>
            <input v-model="form.fecha_emision" type="date" class="border p-2 rounded text-xs focus:ring-1 focus:ring-blue-400 outline-none" />
          </div>-->
        </div>
      </div>

      <!-- Save Button Information -->
      <div class="flex justify-end gap-2 pt-2">
        <button 
          v-if="!isSaved"
          @click="saveInfo"
          class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold uppercase rounded shadow-md transition-colors flex items-center gap-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
          </svg>
          Guardar Información
        </button>
        <button 
          v-else
          @click="printCarnet"
          class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase rounded shadow-md transition-colors flex items-center gap-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          Imprimir Carnet
        </button>
        <button 
          v-if="isSaved"
          @click="resetForm"
          class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-xs font-bold uppercase rounded shadow-md transition-colors"
        >
          Nuevo
        </button>
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
            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Imagen Superior (Reverso)</label>
            <input type="file" @change="handleImage($event, 'reverso_bg')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
            <p class="text-[9px] text-gray-400 mt-1">Logo o cintillo superior para el reverso.</p>
          </div>

          <div class="p-3 bg-white rounded border">
            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">QR Perfil Digital (Reverso)</label>
            <input type="file" @change="handleImage($event, 'reverso_qr')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
          </div>

          <div class="p-3 bg-white rounded border">
            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Sello Institucional (Reverso)</label>
            <input type="file" @change="handleImage($event, 'reverso_sello')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
            <p class="text-[9px] text-gray-400 mt-1">Imagen del sello que irá en la parte inferior izquierda.</p>
          </div>

          <div class="p-3 bg-white rounded border">
            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Firma Autorizada</label>
            <input type="file" @change="handleImage($event, 'reverso_firma')" class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end gap-2 pt-4">
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
import { ref, onMounted } from 'vue';
import Http from '@/utils/Http';
import { alerta } from '@/utils/alert';
import { jsPDF } from 'jspdf';
import html2canvas from 'html2canvas';

const props = defineProps({
  form: Object
});

const emit = defineEmits(['save-config', 'saved']);

const activeTab = ref('info');
const isSaved = ref(false);
const ministerios = ref<any[]>([]);

const fetchMinisterios = async () => {
  try {
    const response = await Http.get('/api/ministerios');
    ministerios.value = response.data.ministerios;
  } catch (error) {
    console.error("Error al cargar ministerios:", error);
  }
};

const fetchLastConfig = async () => {
  try {
    const response = await Http.get('/api/registro/carnets');
    const configs = response.data;
    const lastActive = configs.filter((c: any) => c.estatus).pop();
    
    if (lastActive) {
      props.form.reverso_texto_superior = lastActive.texto_superior;
      props.form.reverso_texto_inferior = lastActive.texto_inferior;
      props.form.reverso_sello_img = lastActive.sello;
      props.form.reverso_firma_img = lastActive.firma;
      props.form.bg_img = lastActive.imagen_fondo;
      props.form.footer_img = lastActive.imagen_pie_pagina;
      props.form.reverso_bg_img = lastActive.imagen_fondo_reverso;
      props.form.reverso_qr_img = lastActive.qr_reverso;
    }
  } catch (error) {
    console.error("Error al cargar configuración inicial:", error);
  }
};

onMounted(() => {
  fetchLastConfig();
  fetchMinisterios();
});

const saveConfig = async () => {
  try {
    const payload = {
      texto_superior: props.form.reverso_texto_superior,
      texto_inferior: props.form.reverso_texto_inferior,
      sello: props.form.reverso_sello_img,
      firma: props.form.reverso_firma_img,
      imagen_fondo: props.form.bg_img,
      imagen_pie_pagina: props.form.footer_img,
      imagen_fondo_reverso: props.form.reverso_bg_img,
      qr_reverso: props.form.reverso_qr_img,
      estatus: true
    };

    const response = await Http.post('/api/registro/carnets', payload);
    alerta('Éxito', 'Configuración guardada exitosamente', 'success');
    emit('saved', response.data);
  } catch (error: any) {
    console.error(error);
    alerta('Error', 'Error al guardar la configuración: ' + (error.response?.data?.error || error.message), 'error');
  }
};

const saveInfo = async () => {
  try {
    const payload = {
      solicitante: props.form.solicitante,
      cedula: props.form.cedula,
      cargo: props.form.cargo,
      oficina: props.form.oficina,
      foto_img: props.form.foto_img
    };

    const response = await Http.post('/api/registro/carnets', payload);
    alerta('Éxito', 'Datos guardados exitosamente', 'success');
    isSaved.value = true;
    emit('saved', response.data);
  } catch (error: any) {
    console.error(error);
    alerta('Error', 'Error al guardar los datos: ' + (error.response?.data?.error || error.message), 'error');
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
      if (type === 'reverso_bg') props.form.reverso_bg_img = base64String;
      if (type === 'reverso_qr') props.form.reverso_qr_img = base64String;
      if (type === 'reverso_sello') props.form.reverso_sello_img = base64String;
      if (type === 'reverso_firma') props.form.reverso_firma_img = base64String;
    };
    reader.readAsDataURL(file);
  }
};

const printCarnet = () => {
  window.print();
};

const resetForm = () => {
  isSaved.value = false;
  props.form.solicitante = '';
  props.form.cedula = '';
  props.form.cargo = '';
  props.form.oficina = '';
  props.form.foto_img = null;
  props.form.reverso_bg_img = null;
  props.form.reverso_qr_img = null;
};

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
