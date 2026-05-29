<script setup lang="ts">
import type { UserData, UserGeografia, UserRegister } from "@/types/votos/voteOne"
import { useRoute } from "vue-router";

const props = defineProps<{
  UserData?: UserData | UserGeografia,
  UserRegister?: any,
  icon: string
  title: string
  hidden?: Function,
  envioData?: () => void,
  funPDF?: () => void,
  buttonText?: string
}>()

const route = useRoute()

const api_url = import.meta.env.VITE_APP_API_URL

</script>

<template>
  <div class="bg-white dark:bg-slate-800 rounded-lg px-6 py-8 ring-1 ring-slate-900/5 shadow-xl z-10 fadeout">
    <div class="flex place-content-center mx-auto">
      <span class="inline-flex items-center justify-center p-2 bg-[#010c41] rounded-3xl shadow-lg w-96">
        <font-awesome-icon class="h-6 w-6 text-white" :icon='props.icon' />
        <h2 class="mx-3 text-white">{{ title }}</h2>
      </span>
    </div>

    <div v-if="UserData" v-for="key in Object.keys(UserData).filter(k => k !== 'id')">
      <div v-if="key != 'foto'">
        <p class="text-slate-600 dark:text-slate-400 my-2 text-sm uppercase"> {{ key.replace(/_/g, ' ') }}</p>
        <h3 class="text-slate-900 dark:text-white mt-0 text-base font-medium tracking-tight uppercase">{{ key ==
          "cédula" ? `V-${UserData[key].toLocaleString("es-ES")}` : UserData[key] }}</h3>
      </div>
    </div>
    

    <div v-else>
      <h3 class="text-slate-900 dark:text-white mt-5 text-base font-medium tracking-tight">¿Ha Asistido?: </h3>
      <div class="grid">
      <img v-if="UserRegister.foto" :src="`${api_url}/storage/${UserRegister.foto}`" class="h-[150px] w-[150px] mx-auto my-4" alt="image">

        <p class="my-5 mx-auto">{{ UserRegister.registro_existente ? "Asistencia ya registrada" : "No" }}.</p>
        <font-awesome-icon icon="person-walking-arrow-right"
          class="text-green-600 h-20 mx-auto aling-center my-5 fa-flip-horizontal"
          v-if="UserRegister.registro_existente == 1" />
        <font-awesome-icon icon="person-walking-arrow-right" class="text-red-600 h-20 mx-auto aling-center my-5"
          v-if="UserRegister.registro_existente == 2" />
      </div>

      <div class="h-30 flex justify-center items-center gap-2" v-if="envioData && UserRegister.registro_existente == 0">
        <button 
          name="si" class=" bg-[#ECA008] hover:bg-[#010c41] text-white font-bold py-2 px-10 rounded-3xl my-8 w-full" @click="envioData">{{ buttonText || 'Participó' }}</button>
          
          <button
          v-if="(route.fullPath == '/fedevida/presencial' || route.fullPath == '/fedevida/correo') && funPDF && UserRegister.registro_fe"
                @click="funPDF"
                class="border-[2px] border-[#010c41] text-[#010c41] rounded-xl w-[20%] py-2" title="Descargar planilla">
                   <font-awesome-icon icon="fa-solid fa-file-pdf" class="text-2xl"/>
          </button>
      </div>
    </div>
  </div>
</template>