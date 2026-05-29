export default [
    {
        path: '/carnets',
        name: 'carnets-list',
        component: () => import('../views/CarnetListView.vue'),
        meta: {
            title: 'Listado de Carnets',
            requiresAuth: true
        }
    },
    {
        path: '/carnets/form',
        name: 'carnets-form',
        component: () => import('../views/CarnetView.vue'),
        meta: {
            title: 'Generador de Carnets',
            requiresAuth: true
        }
    }
];
