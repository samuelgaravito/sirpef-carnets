import Http from "@/utils/Http"

export const getJubilados = async () => {
    const response  = await Http.get(`/api/registro/Jubilados`)
    return response.data
}

export const getItemsCounted = async (fecha_inicio: string, fecha_desde: string, tipoPersona: string) => {
    const response  = await Http.get(`/api/registro/fe-vida-count/?tipoEmpleado=${tipoPersona}&fechaDesde=${fecha_inicio}&fechaHasta=${fecha_desde}`)
    return response.data
}

export const getJubilado = async (cedula: string) => {
    const response  = await Http.get(`/api/findByCedula/fedevida/${cedula}`)
    return response.data
}

export const getSolicitudes = async () => {
    const response  = await Http.get(`/api/registro/solicitudes/fe-de-vida`)
    return response.data
}

export const AcceptSolicitud = async (payload: any) => {
    const response  = await Http.post(`/api/registro/solicitud-estatus/`, payload)
    return response.data
}

export const sendJubilado = async (id: string, payload: any) => {
    const response  = await Http.post(`/api/feDeVida/${id}`, payload)
    return response.data
}