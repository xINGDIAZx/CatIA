// Importamos las funciones necesarias de vue-router para crear el enrutador
import { createRouter, createWebHistory } from "vue-router";

// Importamos todos los componentes que se usarán en las rutas
import Home from "../components/home.vue";           // Componente de la página principal
import Login from "../components/login.vue";         // Componente de login
import Register from "../components/register.vue";   // Componente de registro
import Dashboard from "../dashboard.vue";            // Componente del dashboard principal
import Billeteras from "../components/billeteras.vue"; // Componente de gestión de billeteras
import Perfil from "../components/perfil.vue";       // Componente del perfil de usuario

// Definimos el array de rutas con sus configuraciones
const routes = [
    // Ruta raíz que muestra el componente Home
    { path: "/", component: Home },

    // Ruta para la página de login
    { path: "/login", component: Login },

    // Ruta para la página de registro
    { path: "/register", component: Register },

    // Ruta del dashboard - requiere autenticación
    {
        path: "/dashboard",
        component: Dashboard,
        meta: { requiresAuth: true }  // Metadato que indica que necesita autenticación
    },

    // Ruta de billeteras - requiere autenticación
    {
        path: "/billeteras",
        component: Billeteras,
        meta: { requiresAuth: true }  // Metadato que indica que necesita autenticación
    },

    // Ruta del perfil - requiere autenticación
    {
        path: "/perfil",
        component: Perfil,
        meta: { requiresAuth: true }  // Metadato que indica que necesita autenticación
    }
];

// Creamos la instancia del router con configuración de historial web
const router = createRouter({
    history: createWebHistory(),  // Usa el historial del navegador para URLs limpias
    routes,                       // Array de rutas definido arriba
});

// Guardia de navegación - se ejecuta antes de cada cambio de ruta
router.beforeEach((to, from, next) => {
    // Obtenemos el token de autenticación del localStorage
    const token = localStorage.getItem('token');

    // Verificamos si la ruta requiere autenticación y si no hay token
    if (to.meta.requiresAuth && !token) {
        // Si requiere auth pero no hay token, redirigimos al login
        next('/login');
    } else {
        // Si no requiere auth o hay token, permitimos la navegación
        next();
    }
});

// Exportamos el router para usarlo en la aplicación principal
export default router;
