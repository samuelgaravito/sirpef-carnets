import auth from "@/middleware/auth"

export default [{
    path: "/prescolar",
    name: "prescolar",     
    meta: { middleware: [auth]},      
    component: () => import("@/modules/Prescolar/views/index.vue").then(m => m.default)
},
]
