<script lang="ts" setup>
import type { unid_Adsc } from '@/types/votos/unidAds';
import Select from '@/components/Votos/Select.vue';
import { getDay } from '@/utils/GetDay';
import { onMounted} from 'vue';
import useFIlter from '../../composables/useFIlter';


const props = defineProps<{
  Unidades_Ascritas: unid_Adsc[],
  tipoPersonas: any[],
  PostUnidAsc: Function,
}>()


const {
  GetUnidAsc,
  print,
  SeeFilter,
  getTipoPersona,
  selectedUnid,
  fecha_desde,
  fecha_hasta,
  Listsee,
  empleados,
  selectedEmpleado
} = useFIlter(props)


const $emit = defineEmits(['setTipoPersona', 'ChangueDate'])


onMounted(() => {
  GetUnidAsc()
  getTipoPersona()
  selectedUnid.value = props.Unidades_Ascritas.find(e => e.active == true).nombre || 'ninguna'
})

const selectUnid = async (id: Number) => {
  const unidad = props.Unidades_Ascritas.find(element => element.id == id)
    try {
      await props.PostUnidAsc(id)
      selectedUnid.value = unidad.nombre
    } catch (error) {
      console.log(error)
    } finally {
      Listsee.value = false
    }
}


const selectTipoEmpleado = async (id: Number) => {
  const tipo = empleados.value.find(element => element.id == id)
    try {
      selectedEmpleado.value = tipo.nombre
      $emit('setTipoPersona', {tipo: tipo.id})

      const unidad = props.Unidades_Ascritas.find(element => element.nombre == selectedUnid.value)

      await props.PostUnidAsc(unidad.id || 1)
    } catch (error) {
      console.log(error)
    } finally {
      Listsee.value = false
    }
}


</script>

<template>
  <button id="btn_filter" @click="SeeFilter"
    class="absolute block rounded-2xl h-[50px] top-[90px] right-5 w-[60px] bg-transparent text-[#4E85C8] hover:text-white transition ease-in-out delay-15 z-[100]">
    <font-awesome-icon id="btn_filter" icon="fa-solid fa-bars-staggered" class="h-2/4" />

    <div id="filters" v-if="Listsee" class="w-[80vw] right-0 top-12 md:w-[20vw] mx-auto p-10 rounded-3xl block border shadow-sm text-black bg-white absolute md:right-14 md:top-0 z-20">
      <p class="text-[#B4B5BB] text-2xl text-left">Filtro</p>

      <form @submit.prevent="(e: FormDataEvent) => {$emit('ChangueDate', e); Listsee = false}" class="items-center gap-4 w-full px-2 py-3 text-left">
        <label>Desde:</label>
        <input class="my_inputFilter" type="date" v-model="fecha_desde" :max="getDay()" name="Desde">
        <label>Hasta:</label>
        <input class="my_inputFilter" type="date" v-model="fecha_hasta" :max="getDay()" name="Hasta">
        <button class="capitalize mt-3 mx-auto w-3/4 block py-2 bg-[#4E85C8] text-white border-white border-[1px] rounded-xl font-[600] hover:text-[#4E85C8] hover:bg-white hover:border-[#4E85C8] transition-all">Filtrar</button>
      </form>

      <p class="text-[#B4B5BB] text-xl text-left mb-4">Oficinas</p>
      <Select :width="'w-full mx-auto my-4'" :Unidades_Ascritas="Unidades_Ascritas" :SelectValue="selectUnid" :ActiveUnid="selectedUnid" />


      <p class="text-[#B4B5BB] text-xl text-left mb-4">Tipo de persona</p>
      <Select :width="'w-full mx-auto my-4'" :Unidades_Ascritas="empleados" :SelectValue="selectTipoEmpleado" :ActiveUnid="selectedEmpleado" />
    </div>
  </button>

</template>

<style scoped>
.my_inputFilter {
  @apply w-full mt-2
}
</style>