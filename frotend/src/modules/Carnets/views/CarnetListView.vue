<template>
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
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="carnet in carnets" :key="carnet.id">
            <td class="text-center">{{ carnet.cedula }}</td>
            <td class="text-center">{{ carnet.solicitante }}</td>
            <td class="text-center">
              <button class="text-blue-600 hover:underline font-bold">Ver</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Welcome from "@/components/sirpef/welcome.vue";

const carnets = ref([]);
</script>
