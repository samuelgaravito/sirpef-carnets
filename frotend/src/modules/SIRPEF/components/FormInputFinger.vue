<script setup lang="ts">
import { alerta } from "@/utils/alert";
import { ref } from "vue"

const inputValue = ref('')
const MarginValue = ref(10)

const props = defineProps<{
  FunGetUser: Function,
  finger: boolean
  cort: boolean
}>()

const search = async (e: FormDataEvent) => {
  const form = new FormData(e.currentTarget as HTMLFormElement)
  let cedula: any = form.get("busqueda")
  if (cedula.length == 0) return
  cedula = cedula.replace(/[.V-]/g, "")
  const bool = await props.FunGetUser(cedula)
  MarginValue.value = bool ? 0 : 10
  inputValue.value = ""
}

const changeValue = (e: any) => {
  const newValue = e.target.value.replace(/[^0-9]/g, "");
  const val = parseInt(newValue.slice(0, 8));
  if (inputValue.value.length > 0) inputValue.value = isNaN(val) ? "" : "V-" + val.toLocaleString("es-ES");
}

const SelectFinger = async () => {
  const urlBiometric = 'http://localhost:5000'
  try {
    await fetch(urlBiometric)
    window.open(urlBiometric, '_blank', 'width=500,height=500,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no');
    window.addEventListener('message', function (event) {
      if (event.data.cedula) props.FunGetUser(event.data.cedula)
      window.removeEventListener('message', arguments[0]);
    }, false);
  } catch (error) {
    alerta("Atención", "No se ha podido establecer conexión con <br><strong>'SIRPEF BIOMETRIC'</strong>", "info")
  }
}

</script>

<template>
  <form id="formInput"
    class="fadeout md:w-full md:mx-auto block md:grid grid-cols-2 bg-white md:rounded-2xl overflow-hidden shadow h-auto md:h-96"
    @submit.prevent="search">
    <article class="flex items-center w-full  pb-24 h-56 md:h-3/5 self-center md:pb-0">
      <div
        class="h-full w-full scale-[.5] md:mt-[-20%] md:ml-[12%] md:scale-[.8] place-items-center bg-white">
        <article class="text-center">
          <div class="h-[30vh] borderInfinity p-5 mb-5">
            <img src="/finger.webp" class="h-full " alt="">
          </div>
          <p class="text-2xl font-semibold fadeInfinity">Esperando Huella...</p>
        </article>
      </div>
    </article>
    <article id="busqueda" class="relative items-center gap-5 self-center h-46 mb-10 md:mb-0">
      <label class="block font-bold text-2xl text-center xl:text-left mx-auto">Ingrese el número de cédula: </label>
      <input class="rounded-md mx-auto " type="text" name="busqueda" v-model="inputValue"
        placeholder="Ingrese la cédula" @input="changeValue" />

      <div class="flex gap-2 xl:w-[90%] mx-auto w-full">
        <button title="Buscar la cédula"
          class="xl:w-1/4 w-3/4 bg-[#010c41] block rounded-md font-bold hover:bg-[#1F52C7] text-white mx-auto cursor-pointer transition-all">
          Buscar
        </button>
        <button type="button" @click="SelectFinger" v-if="finger" title="Buscar por huella"
          class="w-[50px!important] border-solid border-[2px] border-[#010c41] block font-bold hover:bg-[#1F52C7] text-[#1F52C7] hover:text-[#ffffff] cursor-pointer transition-all">
          <font-awesome-icon icon="fingerprint" />
        </button>

        <button type="button" @click="$emit('setCortesia')" v-if="cort" title="Entregar Cortesia"
          class="w-[50px!important] border-solid border-[2px] border-[#010c41] block font-bold hover:bg-[#1F52C7] text-[#1F52C7] hover:text-[#ffffff] cursor-pointer transition-all">
          <font-awesome-icon icon="fa-solid fa-handshake" />
        </button>
      </div>
    </article>
  </form>
</template>


<style scoped>
#formInput {
  position: relative;
  z-index: 10;
}

#busqueda {
  margin-top: 10px;
  display: grid;
}

input,
button {
  border-radius: 10px;
  margin: 10px auto;
  height: 45px;
  width: 80%;
  border-color: #1F52C7;
}

button {
  width: 70%;
}

@media (max-width: 900px) {

  #formInput img {
    border-radius: 0% 0% !important;
  }
}



.borderInfinity {
  line-height: 90px;
  font-size: 45px;
  text-align: center;
  color: white;
  position: relative;
  border-radius: 50%;
}

.borderInfinity:after {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  top: -4px;
  left: -4px;
  border: 4px dashed #041E42;
  z-index: 1;
  animation: spin 20s linear infinite;
}


.fadeInfinity {
  animation: fade 2s infinite alternate-reverse;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}


@keyframes fade {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}
</style>