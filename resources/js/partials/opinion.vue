<script setup>
    // Importamos las funciones necesarias de Vue y GSAP para animaciones
    import { onMounted, ref } from 'vue';
    import { gsap } from 'gsap';

    // Creamos una variable reactiva para almacenar la opinión que se mostrará
    const opinion = ref('');

    // Hook que se ejecuta cuando el componente se monta en el DOM
    onMounted(() => {
        // Agregamos un event listener global para escuchar eventos personalizados 'opinion'
        window.addEventListener('opinion', (e) => {
            // Cuando se dispara el evento, actualizamos la opinión con los datos recibidos
            opinion.value = e.detail.opinion;
            // Iniciamos las animaciones de entrada
            startAnimations();
        });
    });

    // Función que maneja las animaciones de entrada del componente
    const startAnimations = () => {
        // Creamos una línea de tiempo de GSAP para secuenciar las animaciones
        const timeline = gsap.timeline();

        // Primero hacemos que el elemento desaparezca (opacidad 0) y se mueva hacia abajo (y: 200)
        timeline.to('#id-opinion', {
            opacity: 0,
            y: 200,
            duration: 1.5,
        }).from('#id-opinion', {
            // Luego hacemos que aparezca desde abajo con una animación suave
            opacity: 0,
            y: 200,
            duration: 1.5,
        })
    }

    // Función para cerrar el componente de opinión
    const cerrar = () => {
        // Animamos la salida del componente
        gsap.to('#id-opinion', {
            opacity: 0, // Hacemos que desaparezca
            y: 200, // Lo movemos hacia abajo
            duration: 1.5, // Duración de la animación
            onComplete: () => {
                // Cuando la animación termina, limpiamos el contenido
                opinion.value = '';
            }
        })
    }
</script>

<template>
    <!-- Contenedor principal que solo se muestra si hay una opinión -->
    <div class="container-fluid" id="id-opinion" v-if="opinion">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <!-- Columna que contiene la imagen del gato y el globo de diálogo -->
                <div class="col-md-6 col-12 d-flex justify-content-center align-items-end position-relative">
                    <!-- Contenedor de la imagen del gato (25% del ancho) -->
                    <div class="w-25">
                        <!-- Imagen del gato consejero -->
                        <img src="../public/catconsejo.png" alt="CatIA" class="img-data">
                    </div>
                    <!-- Contenedor del globo de diálogo (75% del ancho) -->
                    <div class="w-75">
                        <!-- Globo de diálogo con borde morado -->
                        <div class="speech-bubble m-2">
                            <!-- Contenedor del texto con padding derecho -->
                            <div class="speech-bubble-text pe-3">
                                <!-- Párrafo que muestra la opinión -->
                                <p class="parrafos fs-6">{{ opinion }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Botón de cerrar posicionado en la esquina superior derecha -->
                    <div class="position-absolute top-0 end-0 btn btn-cerrar rounded-circle" @click="cerrar">
                        <!-- Ícono de X para cerrar -->
                        <i class="las la-times-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* Estilo para la imagen del gato con efecto de sombra blanca */
    .img-data{
        filter: drop-shadow(0px 0px 30px rgba(255, 255, 255, 1));
    }
    /* Estilos para el globo de diálogo */
    .speech-bubble {
        position: relative; /* Posicionamiento relativo */
        background: white; /* Fondo blanco */
        border: 12px solid #8B5FBF; /* Borde morado grueso */
        border-radius: 40px; /* Bordes redondeados */
        padding: 20px; /* Espaciado interno */
        display: flex; /* Layout flexible */
        align-items: center; /* Centrado vertical */
        justify-content: center; /* Centrado horizontal */
    }
    /* Estilos para el texto dentro del globo */
    .speech-bubble-text {
        font-size: 24px; /* Tamaño de fuente */
        font-weight: bold; /* Texto en negrita */
        color: #333; /* Color del texto */
        text-align: center; /* Texto centrado */
    }
    /* Estilos para el botón de cerrar */
    .btn-cerrar{
        background-color: #8B5FBF; /* Fondo morado */
        color: white; /* Texto blanco */
    }
</style>