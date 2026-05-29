import { reactive, onMounted } from "vue"
import { onBeforeRouteUpdate } from "vue-router"
import useTableGrid from "@/composables/useTableGrid"
import useHttp from "@/composables/useHttp"
import EmployesServices from "@/modules/Auth/services/EmployesService"
import Swal from "sweetalert2"
import { alerta } from "@/utils/alert"
import Http from "@/utils/Http"

type Params =  string | string[][] | Record<string, string> | URLSearchParams | undefined

export default () => {
  const data = reactive({
    rows: [],
    links: [],
    page: "1",
    search: "",
    sort: "",
    direction: ""
  })

  const {  
    errors,
    getError     
  } = useHttp()

  const {
    route,
    router,
    setSearch,
    setSort, 
  } = useTableGrid(data, "/personal")

  const getEmployes = (routeQuery: string) => {  
    
    return EmployesServices.getEmployesAsistencia(routeQuery)
      .then((response) => {
        data.rows = response.data.rows.data
        data.links = response.data.rows.links
        data.search = response.data.search
        data.sort = response.data.sort
        data.direction = response.data.direction   
      })
      .catch((error) => {
        console.log(error)
      })
  }

  onBeforeRouteUpdate(async (to, from) => {     
    if (to.query !== from.query) {        
      await getEmployes(
        new URLSearchParams(to.query as Params).toString()
      )
    }
  })

  onMounted(() => {
    getEmployes(
      new URLSearchParams(route.query as Params).toString()
    )
  })

  return {
    errors,
    data,
    router,
    setSearch,
    setSort
  }
}

