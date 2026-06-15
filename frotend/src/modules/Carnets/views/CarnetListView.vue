<template>
  <Navbar />
  <Welcome title="Listado de Carnets" subtitle="Gestión de carnets emitidos">
    <div class="flex justify-center">
        <router-link to="/carnets/form" class="bg-[#ECA008] hover:bg-[#010c41] text-white font-bold py-3 px-8 rounded-3xl transition-all shadow-lg flex items-center">
          <font-awesome-icon icon="plus" class="mr-2" />
          Nuevo Carnet
        </router-link>
    </div>
  </Welcome>

  <div class="w-[90%] mx-auto mt-10">
    <div class="table-data__wrapper">
      <table class="table-data">
        <thead>
          <tr>
            <th class="text-center">Cédula</th>
            <th class="text-center">Nombre</th>
            <th class="text-center">Ministerio / Oficina</th>
            <th class="text-center">Estatus</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="registro in carnets" :key="registro.id">
            <td class="text-center">{{ registro.evento_persona?.persona?.cedula || registro.evento_persona?.persona?.cedula }}</td>
            <td class="text-center">{{ registro.evento_persona?.persona?.nombre_completo || registro.evento_persona?.persona?.nombre_completo }}</td>
            <td class="text-center">
              {{ registro.evento_persona?.persona?.ministerio?.nombre || 'No asignado' }}
            </td>

            <td class="text-center">
              <span 
                v-if="registro.status === 1 || registro.status === '1' || registro.status === true" 
                class="px-2 py-1 text-[10px] font-bold uppercase rounded-full bg-green-100 text-green-800 border border-green-300"
              >
                Activo
              </span>
              <span 
                v-else 
                class="px-2 py-1 text-[10px] font-bold uppercase rounded-full bg-red-100 text-red-800 border border-red-300"
              >
                Inactivo
              </span>
            </td>
            
            <td class="text-center">
              <div class="flex justify-center gap-3">
                <button @click="openPreview(registro.id)" class="text-blue-600 hover:text-blue-800 font-bold flex items-center">
                  <font-awesome-icon icon="eye" class="mr-1" />
                  Ver
                </button>
                <button class="text-amber-600 hover:text-amber-800 font-bold flex items-center">
                  <font-awesome-icon icon="edit" class="mr-1" />
                  Editar
                </button>
                <button class="text-red-600 hover:text-red-800 font-bold flex items-center">
                  <font-awesome-icon icon="trash" class="mr-1" />
                  Eliminar
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal de Previsualización -->
  <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full p-6 relative">
      <button @click="closePreview" class="absolute top-4 right-4 text-gray-500 hover:text-black text-2xl">
        &times;
      </button>
      
      <div class="text-center mb-6">
        <h3 class="text-xl font-bold text-gray-800">Vista Previa de Carnet</h3>
      </div>

      <div v-if="loadingPreview" class="flex justify-center py-10">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
      </div>

      <div v-else-if="selectedCarnetData" class="flex flex-col items-center">
        <div class="max-h-[70vh] overflow-y-auto w-full flex justify-center py-4">
            <CarnetPreview :data="selectedCarnetData" />
        </div>
        
        <div class="mt-6 flex gap-4">
            <button @click="closePreview" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition-all">
                Cerrar
            </button>
            <button @click="printCarnet" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg transition-all flex items-center">
                <font-awesome-icon icon="print" class="mr-2" />
                Imprimir
            </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Welcome from "@/components/sirpef/welcome.vue";
import Http from "@/utils/Http";
import CarnetPreview from "../components/CarnetPreview.vue";

const router = useRouter();
const carnets = ref([]);
const showModal = ref(false);
const selectedCarnetData = ref(null);
const loadingPreview = ref(false);

const openPreview = async (id) => {
  loadingPreview.value = true;
  showModal.value = true;
  try {
    const response = await Http.get(`/api/registro/carnets/registros/${id}`);
    const reg = response.data;
    
    // Normalize data for CarnetPreview
    selectedCarnetData.value = {
      ...reg,
      solicitante: reg.solicitante,
      cedula: reg.cedula,
      cargo: reg.cargo,
      oficina: reg.oficina,
      foto_img: reg.foto_img,
      // info_carnet triggers fetchLastActiveConfig in the child component
      info_carnet: reg.info_carnet 
    };
  } catch (error) {
    console.error("Error loading preview:", error);
    showModal.value = false;
  } finally {
    loadingPreview.value = false;
  }
};

const closePreview = () => {
  showModal.value = false;
  selectedCarnetData.value = null;
};

const printCarnet = () => {
  window.print();
};

const fetchCarnets = async () => {
  try {
    const response = await Http.get('/api/registro/carnets/registros');
    carnets.value = response.data;
  } catch (error) {
    console.error("Error fetching carnets:", error);
  }
};

onMounted(() => {
  fetchCarnets();
});
</script>
