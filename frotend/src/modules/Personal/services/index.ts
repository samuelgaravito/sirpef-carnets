import Http from "@/utils/Http"

export const postPersona = async (payload: any) => {
    const response = await Http.post(`/api/registro/create`, payload)
    return response
}

export const storePhotoPersona = async (payload: any, id: string) => {
    const response = await Http.post(`/api/registro/foto-persona/${id}`, payload)
    return response
}