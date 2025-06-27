<script setup>
    // Importaciones necesarias de Vue Router y GSAP para animaciones
    import { useRouter } from 'vue-router';
    import { gsap } from 'gsap';
    import { onMounted, onBeforeUnmount, ref } from 'vue';

    // Inicialización del router de Vue
    const router = useRouter();
    // Referencia reactiva para almacenar datos del usuario
    const user = ref(null);
    // Variable para almacenar instancia de SplitText (no se usa en este código)
    let splitInstance = null;

    // Función que maneja las animaciones de entrada para la página principal
    const startAnimations = () => {
        // Si existe una instancia anterior de SplitText, la revierte
        if (splitInstance) {
            splitInstance.revert();
        }
        // Crea una nueva línea de tiempo para las animaciones
        const timeline = gsap.timeline();

        // Animación para el botón de login: aparece desde arriba-izquierda
        timeline.from('.login', {
            opacity: 0,        // Comienza invisible
            y: -100,           // Comienza 100px arriba
            x: -100,           // Comienza 100px a la izquierda
            duration: 1,       // Duración de 1 segundo
        })
        // Animación para el botón de registro: aparece desde arriba-derecha
        .from('.register', {
            opacity: 0,        // Comienza invisible
            y: -100,           // Comienza 100px arriba
            x: 100,            // Comienza 100px a la derecha
            duration: 1,       // Duración de 1 segundo
        })
        // Animación para el contenido principal: aparece desde arriba
        .from('.main', {
            opacity: 0,        // Comienza invisible
            y: -100,           // Comienza 100px arriba
            duration: 1,       // Duración de 1 segundo
            onComplete: () => {
                // Cuando termina la animación, dispara un evento personalizado
                window.dispatchEvent(new CustomEvent('appAnimationsComplete'));
            }
        })
        // Animación para el footer: aparece desde abajo
        .from('.footer', {
            opacity: 0,        // Comienza invisible
            y: 100,            // Comienza 100px abajo
            duration: 1,       // Duración de 1 segundo
        });
    }

    // Función que maneja las animaciones para el dashboard
    const startAnimationsDashboard = () => {
        // Crea una nueva línea de tiempo para las animaciones del dashboard
        const timeline2 = gsap.timeline();

        // Animación secuencial para los elementos del menú (menu-1 a menu-4)
        // Cada elemento aparece desde arriba con 0.5 segundos de duración
        timeline2.from('.menu-1', {
            opacity: 0,        // Comienza invisible
            y: -100,           // Comienza 100px arriba
            duration: 0.5,     // Duración de 0.5 segundos
        })
        .from('.menu-2', {
            opacity: 0,
            y: -100,
            duration: 0.5,
        })
        .from('.menu-3', {
            opacity: 0,
            y: -100,
            duration: 0.5,
        })
        .from('.menu-4', {
            opacity: 0,
            y: -100,
            duration: 0.5,
        });
    }

    // Hook que se ejecuta cuando el componente se monta en el DOM
    onMounted(() => {
        // Inicia las animaciones de entrada
        startAnimations();

        // Obtiene los datos del usuario desde localStorage
        user.value = JSON.parse(localStorage.getItem('user'));

        // Si no hay usuario autenticado, redirige a la página principal
        if (!user.value) {
            router.push('/');
            return;
        }
    });

    // Hook que se ejecuta antes de que el componente se desmonte
    onBeforeUnmount(() => {
        // Limpia la instancia de SplitText si existe
        if (splitInstance) {
            splitInstance.revert();
        }
    });

    // Event listener para iniciar animaciones cuando se navega a home
    window.addEventListener('homeStart', () => {
        startAnimations();
    });

    // Event listener para iniciar animaciones cuando se navega al dashboard
    window.addEventListener('dashboardStart', () => {
        startAnimationsDashboard();
    });

    // Función para cerrar sesión del usuario
    const logout = () => {
        // Elimina el token de autenticación del localStorage
        localStorage.removeItem('token');
        // Elimina los datos del usuario del localStorage
        localStorage.removeItem('user');
        // Redirige a la página principal
        window.location.href = '/';
    };

</script>

<template>
    <!-- Vista para usuarios NO autenticados -->
    <div v-if="!user" class="w-100 d-flex flex-column justify-content-center align-items-center alto">
        <!-- Barra de navegación con botones de login y registro -->
        <nav class="w-75 d-flex justify-content-end">
            <!-- Botón de login con animación -->
            <router-link class="btn border-1 text-white border-amarillo rounded-4 parrafos login" to="/login">
                Login
            </router-link>
            <!-- Botón de registro con animación -->
            <router-link class="btn border-1 text-white border-amarillo rounded-4 mx-2 parrafos register" to="/register">
                Registro
            </router-link>
        </nav>

        <!-- Contenedor principal donde se renderizan las vistas -->
        <main class="w-75 main-alto border-1 border-amarillo rounded-4 mt-2 main">
            <router-view></router-view>
        </main>

        <!-- Footer con copyright -->
        <div class="w-100 d-flex justify-content-center align-items-center footer">
            <p class="text-white mt-2">
                &copy; {{ new Date().getFullYear() }} Hector Mauricio Delgado Diaz.
            </p>
        </div>
    </div>

    <!-- Vista para usuarios autenticados (dashboard) -->
    <div v-else>
        <div class="container">
            <div class="row">
                <!-- Barra de navegación del dashboard -->
                <div class="col-12 d-flex justify-content-start align-items-end p-3">
                    <!-- Logo principal que lleva al dashboard -->
                    <router-link to="/dashboard" class="menu-1">
                        <img src="./public/cat1.png" alt="catIA" class="cat">
                    </router-link>

                    <!-- Botón de billeteras -->
                    <router-link to="/billeteras" class="menu-2">
                        <img src="./public/btn1.png" alt="billeteras" class="cat-menu" role="button">
                    </router-link>

                    <!-- Botón de perfil -->
                    <router-link to="/perfil" class="menu-3">
                        <img src="./public/btn2.png" alt="perfil" class="cat-menu menu-3" role="button">
                    </router-link>

                    <!-- Botón de logout (sin router-link, solo imagen clickeable) -->
                    <img src="./public/btn3.png" alt="salir" class="cat-menu menu-4 mx-2" @click="logout" role="button">
                </div>
            </div>
        </div>
        <!-- Contenedor donde se renderizan las vistas del dashboard -->
        <router-view></router-view>
    </div>
</template>

<style scoped>
    /* Estilo para ocupar toda la altura de la ventana */
    .alto{
        height: 100vh;
    }

    /* Altura del contenedor principal en la vista de login/registro */
    .main-alto{
        height: 40vh;
    }

    /* Tamaño del logo principal */
    .cat{
        height: 80px;
    }

    /* Tamaño de los botones del menú */
    .cat-menu{
        height: 60px;
    }

    /* Efecto hover para los botones del menú: sombra blanca */
    .cat-menu:hover{
        filter: drop-shadow(0px 0px 10px rgba(255, 255, 255, 1));
    }

    /* Media query para dispositivos móviles */
    @media (max-width: 777px) {
        /* En móviles, el contenedor principal ocupa más altura */
        .main-alto{
            height: 80vh;
        }
    }
</style>
