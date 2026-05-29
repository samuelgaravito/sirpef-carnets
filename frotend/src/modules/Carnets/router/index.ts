import auth from "@/middleware/auth"
import admin from "@/middleware/admin"

export default [
    {
        path: '/carnets',
        name: 'carnets-list',
        component: () => import('../views/CarnetListView.vue'),
          meta: { middleware: [auth, admin] },
      
    },
    {
        path: '/carnets/form',
        name: 'carnets-form',
        component: () => import('../views/CarnetView.vue'),
            meta: { middleware: [auth, admin] },
        
    }
];
