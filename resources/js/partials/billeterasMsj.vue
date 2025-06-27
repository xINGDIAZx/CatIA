<!-- Script setup: Define la lógica del componente usando Composition API -->
<script setup>
// Define las propiedades que recibe el componente desde el componente padre
const props = defineProps({
    // Propiedad 'billeteras': Array que contiene las billeteras del usuario
    billeteras: {
        type: Array,        // Tipo de dato: Array
        required: true,     // Es obligatoria (debe ser proporcionada)
    },
});
</script>

<!-- Template: Define la estructura HTML del componente -->
<template>
    <!-- Contenedor principal: Solo se muestra si no hay billeteras (length == 0) -->
    <div class="row d-flex flex-column justify-content-center align-items-center p-3 mt-5" v-if="billeteras.length == 0">
        <!-- Tarjeta principal con diseño responsivo -->
        <div class="card col-md-6 col-12 border-amarillo mb-3 position-relative rounded-4">
            <!-- Badge circular con número "1" en la parte superior -->
            <div class="position-absolute top-0 start-50 translate-middle btn btn-cerrar rounded-circle titulos fs-4">1</div>

            <!-- Fila que contiene el contenido de la tarjeta -->
            <div class="row g-0">
                <!-- Columna izquierda: Contiene el texto explicativo -->
                <div class="col-md-8 col-sm-8 col-12 d-flex align-items-center">
                    <div class="card-body">
                        <!-- Título principal del mensaje -->
                        <h5 class="card-title titulos fs-4 text-white">
                            Debes crear una billetera
                        </h5>
                        <!-- Primer párrafo explicativo -->
                        <p class="card-text parrafos text-white">
                            Para usar nuestra plataforma, debes crear una
                            billetera.
                        </p>
                        <!-- Segundo párrafo de agradecimiento -->
                        <p class="card-text parrafos text-white">
                            ¡Gracias por tu comprensión!
                        </p>
                    </div>
                </div>

                <!-- Columna derecha: Contiene el botón/imagen de acción -->
                <div class="col-md-4 col-sm-4 col-12 p-5">
                    <!-- Enlace que redirige a la página de billeteras -->
                    <router-link to="/billeteras">
                        <!-- Imagen del botón con efecto hover -->
                        <img src="../public/btn1.png" class="img-fluid btn-aux pulse" />
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Estilos CSS específicos para este componente -->
<style scoped>
    /* Estilo para la tarjeta principal */
    .card{
        background-color: transparent;    /* Fondo transparente */
        min-width: 610px!important;      /* Ancho mínimo fijo */
    }

    /* Efecto hover para el botón: Agrega sombra blanca brillante */
    .pulse:hover {
        filter: drop-shadow(0px 0px 10px rgba(255, 255, 255, 1));
    }

    /* Estilo para el badge circular con número */
    .btn-cerrar{
        background-color: #8B5FBF;       /* Color de fondo morado */
        color: white;                     /* Texto blanco */
        width: 30px;                      /* Ancho fijo */
        height: 30px;                     /* Alto fijo */
        border-radius: 50%;               /* Forma circular */
        display: flex;                    /* Flexbox para centrar contenido */
        align-items: center;              /* Centrado vertical */
        justify-content: center;          /* Centrado horizontal */
    }

    /* Media query para dispositivos móviles (pantallas pequeñas) */
    @media (max-width: 576px) {
        .btn-aux{
            width: 100px;                 /* Ancho reducido en móviles */
        }
    }
</style>
