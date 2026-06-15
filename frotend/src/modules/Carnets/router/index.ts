import auth from "@/middleware/auth"
import admin from "@/middleware/admin"

export default [
    {
        path: '/carnets',
        name: 'carnets-list',
        component: () => import('../views/CarnetListView.vue'),
        meta: { middleware: [auth, admin] },
        // CORRECCIÓN: "children" en singular
        children: [
            {
                // Al unirse con el padre (/carnets) la ruta final será: /carnets/registros/:id
                path: 'registros/:id', 
                name: 'carnets-registros',
                component: () => import('../views/CarnetDetailView.vue'),
                meta: { middleware: [auth, admin] },
            }
        ]
    },
    {
        path: '/carnets/form',
        name: 'carnets-form',
        component: () => import('../views/CarnetView.vue'),
        meta: { middleware: [auth, admin] },
    }
];