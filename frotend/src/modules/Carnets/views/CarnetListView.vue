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
                <router-link :to="`/carnets/registros/${registro.id}`" class="text-blue-600 hover:text-blue-800 font-bold flex items-center">
                  <font-awesome-icon icon="eye" class="mr-1" />
                  Ver
                </router-link>
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

</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Welcome from "@/components/sirpef/welcome.vue";
import Http from "@/utils/Http";

const router = useRouter();
const carnets = ref([]);

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
