import Http from "@/utils/Http";

export default {
  getEmployes(query) {  
    return Http.get(`/api/registro/?${query}`);
  }, 

  getEmployesAsistencia(query) {  
    return Http.get(`/api/registro/registroAsistencia/?${query}`);
  }, 
  getTipoEmpleado() {
    return Http.get('/api/registro/tipoempleado')
  },
  deleteVoto (id) {
    return Http.delete(`/api/registro/delete-vote/${id}`);
  }
 }
