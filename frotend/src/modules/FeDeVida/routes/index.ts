import auth from "@/middleware/auth"
import admin from "@/middleware/admin"

export default [
    {
        path: "/fedevida/form",
        name: "fedevidaForm",  
        meta: {layout: "empty" },
        component: () => import("@/modules/FeDeVida/views/Register.vue").then(m => m.default)
    },

    {
        path: "/fedevida/presencial",
        name: "fedevidaPresencial",
        meta: { middleware: [auth, admin] },
        component: () => import("@/modules/FeDeVida/views/participacionPresencial.vue").then(m => m.default)
    },

    {
        path: "/fedevida/correo",
        name: "fedevidaCorreo",
        meta: { middleware: [auth, admin] },
        component: () => import("@/modules/FeDeVida/views/participacionEmail.vue").then(m => m.default)
    },
{
    path: "/fedevida/Solicitudes",
    name: "fedevidaSolis",
    meta: { middleware: [auth, admin] },
    component: () => import("@/modules/FeDeVida/views/solicitudesJubi.vue").then(m => m.default),
},
{
    path: "/fedevida/checkpdf/:cedula",
        meta: {layout: "empty" },
        name: "checkpdf",
    component: () => import("@/modules/FeDeVida/views/checkPDF.vue").then(m => m.default),
}
]
