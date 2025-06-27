<script setup>
    // ===== IMPORTS =====
    // Importa ref para crear variables reactivas y onMounted para el ciclo de vida del componente
    import { ref, onMounted } from 'vue';
    // Importa GSAP para crear animaciones fluidas y profesionales
    import { gsap } from 'gsap';
    // Importa el servicio personalizado para comunicarse con la API de CatIA
    import { catiaService } from '../services/catiaService';

    // ===== VARIABLES REACTIVAS =====
    // Variable reactiva que almacena el mensaje del consejo recibido de la API
    const mensaje = ref('');
    // Variable reactiva que almacena los datos del usuario autenticado
    const user = ref(null);

    // ===== FUNCIONES DE CONTROL DE FRECUENCIA =====
    // Función que verifica si debe mostrar un consejo hoy
    // Evita mostrar múltiples consejos en el mismo día
    const checkAndShowConsejo = () => {
        // Obtiene la fecha del último consejo mostrado desde el almacenamiento local
        const lastShown = localStorage.getItem('lastConsejoDate');
        // Crea un objeto de fecha actual
        const now = new Date();
        // Convierte la fecha a string para comparación (ej: "Mon Dec 16 2024")
        const today = now.toDateString();

        // Si no hay fecha guardada O la fecha guardada es diferente a hoy
        if (!lastShown || lastShown !== today) {
            // Llama a la función para obtener un nuevo consejo
            consejo();
            // Guarda la fecha actual para evitar mostrar otro consejo hoy
            localStorage.setItem('lastConsejoDate', today);
        }
    }

    // Función que resetea el sistema de consejos a las 23:59
    // Permite mostrar un nuevo consejo al día siguiente
    const checkAndResetConsejo = () => {
        // Obtiene la fecha y hora actual
        const now = new Date();
        // Extrae la hora actual (0-23)
        const hours = now.getHours();
        // Extrae los minutos actuales (0-59)
        const minutes = now.getMinutes();

        // Si es exactamente las 23:59 (11:59 PM)
        if (hours === 23 && minutes === 59) {
            // Elimina la fecha guardada para permitir un nuevo consejo mañana
            localStorage.removeItem('lastConsejoDate');
        }
    }

    // ===== HOOK DE CICLO DE VIDA =====
    // Se ejecuta cuando el componente se monta en el DOM
    onMounted(() => {
        // Obtiene el token de autenticación desde el almacenamiento local
        const token = localStorage.getItem('token');
        // Obtiene los datos del usuario desde el almacenamiento local
        const userData = localStorage.getItem('user');

        // Si no hay token O no hay datos de usuario, redirige al inicio
        if (!token || !userData) {
            router.push('/');
            return; // Sale de la función
        }

        // Convierte el string JSON de usuario a objeto y lo asigna a la variable reactiva
        user.value = JSON.parse(userData);
        // Verifica si debe mostrar un consejo hoy
        checkAndShowConsejo();

        // Configura un intervalo que se ejecuta cada 60,000ms (1 minuto)
        // Para verificar si debe resetear el sistema de consejos
        setInterval(checkAndResetConsejo, 60000);
    });

    // ===== FUNCIÓN PRINCIPAL PARA OBTENER CONSEJO =====
    // Función asíncrona que obtiene un consejo personalizado desde la API
    const consejo = async () => {
        try {
            // Prepara los datos del usuario para enviar a la API
            const userData = {
                nombre_usuario: user.value.name, // Nombre del usuario
                ciudad: user.value.ciudad // Ciudad del usuario
            };

            // Llama al servicio de CatIA para obtener un consejo personalizado
            const data = await catiaService.getConsejo(userData);
            // Asigna la respuesta de la API a la variable reactiva
            mensaje.value = data;
            // Inicia las animaciones de entrada del componente
            startAnimations();
        } catch (error) {
            // Si hay un error en la petición, lo registra en la consola
            console.error('Error al obtener el consejo:', error);
        }
    }

    // ===== FUNCIONES DE ANIMACIÓN =====
    // Función que inicia las animaciones de entrada del componente
    const startAnimations = () => {
        // Crea una línea de tiempo de GSAP para coordinar animaciones
        const timeline = gsap.timeline();

        // Animación de entrada: el componente aparece desde abajo con fade in
        timeline.from('#id-consejo', {
            opacity: 0, // Comienza completamente transparente
            y: 200, // Comienza 200 píxeles abajo de su posición final
            duration: 1.5, // La animación dura 1.5 segundos
        })
    }

    // Función que cierra el componente con animación de salida
    const cerrar = () => {
        // Animación de salida: el componente se desliza hacia abajo y desaparece
        gsap.to('#id-consejo', {
            opacity: 0, // Se vuelve completamente transparente
            y: 200, // Se mueve 200 píxeles hacia abajo
            duration: 1.5, // La animación dura 1.5 segundos
            onComplete: () => {
                // Cuando termina la animación, limpia el mensaje
                // Esto oculta el componente completamente
                mensaje.value = '';
            }
        })
    }


</script>

<template>
    <!-- ===== ESTRUCTURA HTML ===== -->
    <!-- Contenedor principal del componente -->
    <!-- v-show="mensaje" - Solo se muestra cuando hay un mensaje -->
    <!-- container-fluid - Ocupa todo el ancho disponible -->
    <div class="container-fluid" id="id-consejo" v-show="mensaje">
        <!-- Contenedor interno con ancho limitado -->
        <div class="container">
            <!-- Fila centrada horizontalmente -->
            <div class="row d-flex justify-content-center">
                <!-- Columna que contiene la imagen y el globo de diálogo -->
                <!-- col-md-6 - En pantallas medianas ocupa 6 columnas (mitad) -->
                <!-- col-12 - En pantallas pequeñas ocupa 12 columnas (todo el ancho) -->
                <!-- d-flex justify-content-center - Centra el contenido horizontalmente -->
                <!-- align-items-end - Alinea los elementos al final (abajo) -->
                <!-- position-relative - Permite posicionamiento absoluto de elementos hijos -->
                <div class="col-md-6 col-12 d-flex justify-content-center align-items-end position-relative">
                    <!-- Contenedor de la imagen del gato (25% del ancho disponible) -->
                    <div class="w-25">
                        <!-- Imagen del gato consejero -->
                        <img src="../public/catconsejo.png" alt="CatIA" class="img-data">
                    </div>
                    <!-- Contenedor del globo de diálogo (75% del ancho disponible) -->
                    <div class="w-75">
                        <!-- Globo de diálogo con margen -->
                        <div class="speech-bubble m-2">
                            <!-- Contenedor del texto del consejo -->
                            <div class="speech-bubble-text">
                                <!-- Párrafo que muestra el consejo -->
                                <!-- mensaje.response - Accede a la propiedad response del objeto mensaje -->
                                <!-- parrafos fs-6 - Clases de Bootstrap para estilo de texto -->
                                <p class="parrafos fs-6">{{ mensaje.response }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Botón de cerrar posicionado absolutamente en la esquina superior derecha -->
                    <!-- position-absolute top-0 end-0 - Posiciona en la esquina superior derecha -->
                    <!-- btn btn-cerrar rounded-circle - Estilo de botón circular -->
                    <!-- @click="cerrar" - Ejecuta la función cerrar al hacer clic -->
                    <div class="position-absolute top-0 end-0 btn btn-cerrar rounded-circle" @click="cerrar">
                        <!-- Ícono de X para cerrar -->
                        <!-- las la-times-circle - Clase de Line Awesome para el ícono -->
                        <!-- display-6 - Tamaño grande del ícono -->
                        <i class="las la-times-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* ===== ESTILOS CSS ===== */

    /* Estilo para la imagen del gato */
    .img-data{
        /* Aplica un efecto de sombra brillante blanca alrededor de la imagen */
        /* drop-shadow - Crea una sombra que sigue la forma de la imagen */
        /* 0px 0px 30px - Sin desplazamiento, con 30px de desenfoque */
        /* rgba(255, 255, 255, 1) - Color blanco completamente opaco */
        filter: drop-shadow(0px 0px 30px rgba(255, 255, 255, 1));
    }

    /* Estilo del globo de diálogo */
    .speech-bubble {
        position: relative; /* Permite posicionamiento de elementos hijos */
        background: white; /* Fondo blanco */
        border: 12px solid #8B5FBF; /* Borde morado grueso de 12px */
        border-radius: 40px; /* Bordes muy redondeados para efecto de globo */
        padding: 20px; /* Espaciado interno de 20px */
        display: flex; /* Layout flexible para centrar contenido */
        align-items: center; /* Centra verticalmente el contenido */
        justify-content: center; /* Centra horizontalmente el contenido */
    }

    /* Estilo del texto dentro del globo de diálogo */
    .speech-bubble-text {
        font-size: 24px; /* Tamaño de fuente grande para legibilidad */
        font-weight: bold; /* Texto en negrita para destacar */
        color: #333; /* Color de texto gris oscuro para buen contraste */
        text-align: center; /* Texto centrado horizontalmente */
    }

    /* Estilo del botón de cerrar */
    .btn-cerrar{
        background-color: #8B5FBF; /* Fondo morado que coincide con el borde del globo */
        color: white; /* Texto blanco para contraste */
    }
</style>