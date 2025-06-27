<script setup>
// Importamos las funciones necesarias de Vue 3 Composition API
import { ref, onMounted, defineAsyncComponent } from 'vue';
// Importamos el router para navegación entre páginas
import { useRouter } from 'vue-router';
// Importamos AOS (Animate On Scroll) para animaciones
import AOS from 'aos';
// Importamos los estilos CSS de AOS
import 'aos/dist/aos.css';

// Inicializamos AOS para activar las animaciones al hacer scroll
AOS.init();

// Definimos componentes asíncronos para cargar solo cuando se necesiten (lazy loading)
// Esto mejora el rendimiento inicial de la aplicación
const Consejo = defineAsyncComponent(() => import('./partials/consejo.vue'));
const BilleterasMsj = defineAsyncComponent(() => import('./partials/billeterasMsj.vue'));
const CiudadMsj = defineAsyncComponent(() => import('./partials/ciudadMsj.vue'));
const Movimientos = defineAsyncComponent(() => import('./components/movimientos.vue'));

// Creamos una instancia del router para manejar la navegación
const router = useRouter();

// Definimos variables reactivas usando ref()
// user: almacena los datos del usuario autenticado
const user = ref(null);
// billeteras: almacena la lista de billeteras del usuario
const billeteras = ref([]);
// ciudad: almacena la información de la ciudad del usuario
const ciudad = ref(null);

// Hook del ciclo de vida que se ejecuta cuando el componente se monta en el DOM
onMounted(() => {
    // Verificamos si existe un token de autenticación en el localStorage
    const token = localStorage.getItem('token');
    // Verificamos si existen datos del usuario en el localStorage
    const userData = localStorage.getItem('user');

    // Si no hay token o datos de usuario, redirigimos al login
    if (!token || !userData) {
        router.push('/');
        return;
    }

    // Convertimos los datos del usuario de JSON a objeto y los asignamos
    user.value = JSON.parse(userData);
    // Disparamos un evento personalizado para notificar que el dashboard ha iniciado
    window.dispatchEvent(new CustomEvent('dashboardStart'));
    // Asignamos las billeteras del usuario a la variable reactiva
    billeteras.value = user.value.billeteras;
    // Asignamos la ciudad del usuario a la variable reactiva
    ciudad.value = user.value.ciudad;
});

</script>

<template>
    <!-- Contenedor principal con animación de entrada -->
    <div class="container" data-aos="fade-up">
        <!-- Componente que muestra mensajes relacionados con las billeteras -->
        <BilleterasMsj :billeteras="billeteras" />
        <!-- Componente que muestra mensajes relacionados con la ciudad -->
        <CiudadMsj :ciudad="ciudad" />
    </div>

    <!-- Contenedor condicional que solo se muestra si hay billeteras y ciudad -->
    <div class="container" v-if="billeteras.length > 0 && ciudad != null">
        <!-- Componente que muestra los movimientos financieros -->
        <Movimientos></Movimientos>
    </div>

    <!-- Componente de consejos que se muestra fijo en la parte inferior -->
    <Consejo class="consejo"></Consejo>
</template>

<style scoped>
    /* Estilos específicos para el componente consejo */
    .consejo {
        position: fixed; /* Posición fija en la pantalla */
        bottom: 10px;   /* Distancia desde la parte inferior */
    }
</style>
