<script setup>
// Define las propiedades que recibe el componente desde el componente padre
const props = defineProps({
    // Propiedad 'ciudad' que debe ser una cadena de texto y es obligatoria
    ciudad: {
        type: String,        // Tipo de dato: String
        required: true,      // Es obligatoria (no puede ser null/undefined)
    },
});
</script>

<template>
    <!-- Contenedor principal que se muestra solo si la ciudad es null -->
    <!-- Usa clases de Bootstrap para centrar y dar espaciado -->
    <div class="row d-flex flex-column justify-content-center align-items-center p-3" v-if="ciudad == null">
        <!-- Tarjeta principal con borde amarillo y esquinas redondeadas -->
        <!-- col-md-6: ocupa 6 columnas en pantallas medianas, col-12: ocupa todo en móviles -->
        <div class="card col-md-6 col-12 border-amarillo mb-3 position-relative rounded-4">
            <!-- Botón circular con número "2" posicionado en la esquina superior -->
            <!-- translate-middle: centra el elemento horizontalmente -->
            <div class="position-absolute top-0 start-50 translate-middle btn btn-cerrar rounded-circle titulos fs-4">2</div>

            <!-- Fila que contiene el contenido de la tarjeta -->
            <div class="row g-0">
                <!-- Columna izquierda: texto y contenido principal -->
                <!-- col-md-8: 8 columnas en pantallas medianas, col-sm-8: 8 en pequeñas, col-12: todo en móviles -->
                <div class="col-md-8 col-sm-8 col-12 d-flex align-items-center">
                    <!-- Cuerpo de la tarjeta con el contenido textual -->
                    <div class="card-body">
                        <!-- Título principal de la tarjeta -->
                        <h5 class="card-title titulos fs-4 text-white">Completa tu perfil</h5>
                        <!-- Párrafo explicativo sobre completar el perfil -->
                        <p class="card-text parrafos text-white">Para poder ofrecerte los mejores servicios, necesitamos que completes tu perfil.</p>
                        <!-- Párrafo de agradecimiento -->
                        <p class="card-text parrafos text-white">¡Gracias por tu comprensión!</p>
                    </div>
                </div>

                <!-- Columna derecha: imagen/botón de acción -->
                <!-- col-md-4: 4 columnas en pantallas medianas, col-sm-4: 4 en pequeñas, col-12: todo en móviles -->
                <div class="col-md-4 col-sm-4 col-12 p-5">
                    <!-- Enlace que redirige a la página de perfil -->
                    <router-link to="/perfil">
                        <!-- Imagen del botón con efecto de pulso al hacer hover -->
                        <img src="../public/btn2.png" class="img-fluid btn-aux pulse">
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* Estilos específicos para la tarjeta */
    .card{
        background-color: transparent;    /* Fondo transparente */
        min-width: 610px!important;      /* Ancho mínimo de 610px (importante para sobrescribir otros estilos) */
    }

    /* Efecto de resplandor blanco al pasar el mouse sobre elementos con clase pulse */
    .pulse:hover {
        filter: drop-shadow(0px 0px 10px rgba(255, 255, 255, 1));
    }

    /* Estilos para el botón circular con número */
    .btn-cerrar{
        background-color: #8B5FBF;       /* Color de fondo morado */
        color: white;                     /* Color del texto blanco */
        width: 30px;                      /* Ancho de 30px */
        height: 30px;                     /* Alto de 30px */
        border-radius: 50%;               /* Bordes completamente redondeados (círculo) */
        display: flex;                    /* Usa flexbox para centrar contenido */
        align-items: center;              /* Centra verticalmente */
        justify-content: center;          /* Centra horizontalmente */
    }

    /* Estilos responsivos para pantallas pequeñas (móviles) */
    @media (max-width: 576px) {
        .btn-aux{
            width: 100px;                 /* Ancho fijo de 100px en móviles */
        }
    }
</style>