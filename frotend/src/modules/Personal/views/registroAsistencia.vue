<script setup lang="ts">
import convertDateISOToHour from '@/utils/convertDateISOToHour';
import useEmployesAsistencia from '../composables/useEmployesAsistencia';
import { alerta } from '@/utils/alert';

const {
  data,
} = useEmployesAsistencia()


const findEntrada = (cedula: string) => {
  const persona = data.rows.find(e => e.cedula == cedula && e.voto === true)
  if (!persona) return 'Esperando entrada'

  return convertDateISOToHour(persona.created_at)
}

const findSalida = (cedula: string) => {
  const persona = data.rows.find(e => e.cedula == cedula && e.voto === false)
  if (!persona) return 'Esperando salida'

  return convertDateISOToHour(persona.created_at)
}

const addFinger = async () => {
        const urlBiometric = 'http://localhost:5000/formregis'
        try {
            await fetch(urlBiometric)
            window.open(urlBiometric, '_blank', 'width=500,height=500,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no');
        } catch (error) {
            alerta("Atención", "No se ha podido establecer conexión con <br><strong>'SIRPEF BIOMETRIC'</strong>", "info")
        }
    }

</script>

<template>
    <div class="h-full bg-[#F4F5FA] px-4 shadow border overflow-auto">
      
      <article class="flex justify-between items-center w-full md:px-20">
        <h2 class="text-2xl my-6 font-bold text-center">Registro de asistencia</h2>
        <button @click="addFinger" class="bg-[#ECA008] hover:bg-[#010c41] text-white font-bold py-2 px-10 rounded-3xl block my-5">Agregar nueva huella</button>        
      </article>


      <div class="">
        <table class="table-data">
          <thead>
            <tr class="">
              <th class="">Asistencia</th>
              <th class="">
                Nombre completo
              </th>
              <th class="">
                Cédula
              </th>
              <th class="">
                Entrada
              </th>
              <th class="">
                Salida
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="row in data.rows.filter((item, index, self) => index === self.findIndex((t) => t.cedula == item.cedula))"
              :key="row.id" class="text-center">
              <td>
                <div v-if="row.voto != null">
                  <font-awesome-icon title="Si asistió" class="text-green-600 scale-[1.4]" v-if="row.voto"
                    icon="fa-solid fa-user-check" />
                  <font-awesome-icon title="No asistió" class="text-red-600 scale-[1.4]" v-else-if="row.voto == false"
                    icon="fa-solid fa-user-xmark" />
                </div>
                <font-awesome-icon title="Sin registro" class="text-[#ECA008] scale-[1.4]" v-else
                  icon="fa-solid fa-user-clock" />
              </td>

              <td class="">
                {{ row.nombre_completo }}
              </td>
              <td class="">
                {{ row.cedula }}
              </td>
              <td class="">
                {{ findEntrada(row.cedula) }}
              </td>
              <td class="">
                {{ findSalida(row.cedula) }}
              </td>
            </tr>
            <tr v-if="data.rows.length == 0">
              <td class="text-center" colspan="5">Usuarios no encontrados.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</template>