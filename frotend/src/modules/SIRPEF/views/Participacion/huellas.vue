<script setup lang="ts">
// @ts-nocheck
import CardInfoUser from '@/components/Votos/CardInfoUser.vue';
import FormInputFinger from '@/modules/SIRPEF/components/FormInputFinger.vue';
import { obtenerHoraActual } from "@/utils/GetHora"
import Banner from '@/components/Votos/Banner.vue';
import Welcome from '@/components/sirpef/welcome.vue';
import { onMounted } from 'vue';
import useFingers from '../../composables/useFingers';
import Loader from '@/components/Votos/loader.vue';
import convertDateISOToHour from '@/utils/convertDateISOToHour';
import { useRoute } from 'vue-router';

const route = useRoute()

const { GetUser, result, hidden, popupStatus, popup, envioData, Loading, SelectFinger, checkBiomtricStatus, biomtricAtive } = useFingers()


onMounted(() => {
  checkBiomtricStatus()
  SelectFinger()
  if(route.params.ci) GetUser(route.params.ci)
})


</script>

<template>

  <main class="grid place-items-center h-[80vh]" v-if="!biomtricAtive">
    <article class="text-center">
      <Loader />
      <p class="text-3xl font-semibold fadeInfinity">Intentando conectarse a <strong>SIRPEF BIOMETRIC</strong></p>
    </article>
  </main>

  <main class="bg-white" v-else>

    <div class="col-start-1 col-end-3">
      <Welcome title="Gestión de asistencia laboral"
        subtitle="En este apartado se puede registrar la hora de Entrada y de Salida del personal de Recursos Humanos del MPPEF">
        <FormInputFinger :FunGetUser="GetUser" :finger="false" :cort="false" />
      </Welcome>
    </div>

    <section class="results my-10 w-full md:w-3/4" v-if="Object.keys(result).length > 0">
      <CardInfoUser :UserData="result[0]" title="Datos personales" icon="fa-solid fa-user" />
      <CardInfoUser :UserData="result[1]" title="Ubicación" icon="fa-solid fa-location-dot" />
      <CardInfoUser :UserRegister="result[2]" title="Registro" icon="fa-solid fa-check-to-slot" :envioData="envioData"/>
    </section>

  </main>
</template>

<style scoped>
#reportes {
  width: 100%;
  max-height: 90vh;
  margin: 0 auto;
}

#busqueda input:hover~button {
  background-color: #0057b3;
}

.results {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 20px;
  margin: 30px auto;
}

@media (max-width: 900px) {

  #contenedor #banner {
    display: none;
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