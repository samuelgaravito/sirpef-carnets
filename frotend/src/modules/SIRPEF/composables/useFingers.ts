import type { UserData } from "@/types/votos/voteOne"
import {ref } from "vue"
import Http from "@/utils/Http";
import {alerta} from "../../../utils/alert"
import { alertQuestion } from "@/utils/alertQuestion";
import { onBeforeRouteLeave, useRoute, useRouter } from "vue-router";

export default () => {

    const popup = ref<HTMLElement>(null)
    const result = ref({} as UserData)
    const popupStatus = ref(0)
    const Loading = ref(false)
    const cortesia = ref(false)
    const biomtricAtive = ref(false)
    const cortesias = ref ({} as any)
    const controller = new AbortController()
    const router = useRouter()

    const GetUser = async (cedula: string) => {
        result.value = {} as UserData
        try {
            const res = await Http.get(`/api/registro/search/${cedula}`);
            result.value = res.data
            //await envioData()
            return true
        } catch (error) {
            const {response} = error
            alerta("error", 'No se pudo encontrar la persona', "info")
        }
    }

    const hidden = (e?: PointerEvent, value?: number) => {
        const target = e.target as HTMLElement
        if (e && target.tagName === "SECTION") {
            popup.value.style.display = "none"
        } else {
            if(target.tagName === "BUTTON") {
                const button = e.target as HTMLButtonElement
                if(button.name === "si") popupStatus.value = value   
            }         
            popup.value.style.display = "grid"
        }
    }

    const checkBiomtricStatus = async () => {
        if (Loading.value) return SelectFinger()
        const urlBiometric = 'http://localhost:5000'
        try {
            await fetch(urlBiometric)
            /*window.open(urlBiometric, '_blank', 'width=500,height=500,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no');
            window.addEventListener('message', function(event) {

                if(event.data.cedula == 'no') {
                    alerta('info', 'No se encuentra la huella', 'error')
                    setTimeout(() => {
                        location.reload()
                    }, 2000);
                }
                else registerWithFinger(event.data.cedula)
                window.removeEventListener('message', arguments[0]);
            }, false);*/
            return biomtricAtive.value = true
        } catch (error) {
            alerta("Atención", "No se ha podido establecer conexión con <br><strong>'SIRPEF BIOMETRIC'</strong>", "info")
            return biomtricAtive.value = false
        }
    }



    const SelectFinger = async () => {
        if (Loading.value) return SelectFinger()
        const urlBiometric = 'http://localhost:5000'
        try {
            const res = await fetch('http://localhost:5000/init_finger', {
                method: 'POST',
                signal: controller.signal, 
            })
            const cedula = await res.text()
            
            if(cedula == 'no') {
                alerta('info', 'No se encuentra la huella', 'error')
                setTimeout(() => {
                    location.reload()
                }, 2000);
            }
            else registerWithFinger(cedula)

            return

            window.open(urlBiometric, '_blank', 'width=500,height=500,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no');
            window.addEventListener('message', function(event) {

                window.removeEventListener('message', arguments[0]);
            }, false);
        } catch (error) {
            if (error.name === 'AbortError') return
            alerta("Atención", "No se ha podido establecer conexión con <br><strong>'SIRPEF BIOMETRIC'</strong>", "info");
        }
    }




    onBeforeRouteLeave(() => {
      if (controller) {
        controller.abort();
      }
      return true;
    });


    const envioData = async () => {
        const choice = await alertQuestion('Info', '¿Estas seguro?', 'question')
        if(!choice) return
        Loading.value = true

        const data = {
            descripcion: 'asistencia',
            persona_id: result.value[0].id 
        }

        try {
            const response = await Http.post("/api/registro/asistencias", data);
            //alerta('Enviado', response.data.msg, 'success')
            Loading.value = false

            setTimeout(() => {
                //SelectFinger()
                location.reload()
            }, 1000);
        } catch (error) {
            const {response} = error
            alerta("error", response.data.msg || 'No se pudo registrar', "info")
            Loading.value = false
            setTimeout(() => {
                location.reload()
            }, 1000);
        }
        finally{
            result.value = {} as UserData
            popup.value.style.display = "none"
        }
    }


    const registerWithFinger = async (cedula: string) => {
        try {
            const res = await Http.get(`/api/registro/search/${cedula}`);
            const data = {
                descripcion: 'asistencia',
                persona_id: res.data[0].id 
            }
            const response = await Http.post("/api/registro/asistencias", data);
            //alerta('Enviado', response.data.msg, 'success')
            location.replace(`${location.origin}/finger/${res.data[0].cedula}`)
           /* setTimeout(() => {
                //SelectFinger()
            }, 4000);*/
        } catch (error) {
            let message = ''

            if(error.response) {
                message = error.response.data.msg || 'Ocurrio un error inesperado'
            }

            alerta('info', message, 'error')
            setTimeout(() => {
                //SelectFinger()
                location.reload()
            }, 2000);
        }
        finally{
            result.value = {} as UserData
        }
    }



    return {
        result,
        popup,
        popupStatus,
        Loading,
        cortesia,
        cortesias,
        GetUser,
        hidden,
        alerta,
        envioData,
        SelectFinger,
        biomtricAtive,
        checkBiomtricStatus
    }
}