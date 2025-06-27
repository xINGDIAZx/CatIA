<script setup>
    // Importamos useRouter de Vue Router para la navegación entre páginas
    import { useRouter } from 'vue-router';
    // Importamos GSAP (GreenSock Animation Platform) para animaciones avanzadas
    import { gsap } from 'gsap';
    // Importamos el plugin SplitText de GSAP para dividir texto en palabras/caracteres
    import { SplitText } from "gsap/SplitText";
    // Importamos hooks del ciclo de vida de Vue
    import { onMounted, onBeforeUnmount } from 'vue';

    // Registramos el plugin SplitText en GSAP para poder usarlo
    gsap.registerPlugin(SplitText);

    // Creamos una instancia del router para navegación
    const router = useRouter();
    // Variable para almacenar la instancia de SplitText y poder revertirla después
    let splitInstance = null;

    // Función que inicia la animación del texto
    const startAnimation = () => {
        // Si ya existe una instancia de SplitText, la revertimos para limpiar
        if (splitInstance) {
            splitInstance.revert();
        }
        // Mostramos el párrafo (estaba oculto inicialmente)
        gsap.set("#parrafo", { display: "block" });
        // Creamos una nueva instancia de SplitText que divide el texto en palabras
        splitInstance = SplitText.create("#parrafo", { type: "words" });
        // Animamos cada palabra del texto con un efecto de entrada
        gsap.from(splitInstance.words, {
            y: 20,           // Las palabras aparecen 20px más abajo
            autoAlpha: 0,    // Comienzan completamente transparentes
            stagger: 0.1     // Cada palabra aparece 0.1 segundos después que la anterior
        });
    };

    // Hook que se ejecuta cuando el componente se monta en el DOM
    onMounted(() => {
        // Agregamos un listener para el evento personalizado 'appAnimationsComplete'
        // Este evento se dispara cuando otras animaciones de la app terminan
        window.addEventListener('appAnimationsComplete', startAnimation);
    });

    // Hook que se ejecuta antes de que el componente se desmonte
    onBeforeUnmount(() => {
        // Removemos el listener para evitar memory leaks
        window.removeEventListener('appAnimationsComplete', startAnimation);
        // Revertimos la instancia de SplitText para limpiar
        if (splitInstance) {
            splitInstance.revert();
        }
    });

    // Función para navegar a la página principal
    const goHome = () => {
        // Disparamos el evento personalizado para iniciar la animación
        window.dispatchEvent(new CustomEvent('appAnimationsComplete'));
        // Navegamos a la ruta raíz '/'
        router.push('/');
    }
</script>

<template>
    <div>
        <!-- Contenedor principal con flexbox centrado -->
        <div class="w-100 d-flex justify-content-center align-items-center alto">
            <!-- Contenedor para la imagen del gato -->
            <div class="d-flex justify-content-end align-items-center gato">
                <!-- Imagen del gato con clases de Bootstrap para responsividad -->
                <img src="../public/cat1.png" alt="cat1" class="img-fluid img-data mx-5">
            </div>
            <!-- Contenedor para el diálogo/texto -->
            <div class="d-flex flex-column justify-content-center align-items-center alto p-5 dialog">
                <!-- Título principal con ID para posibles animaciones -->
                <h1 class="titulos fs-1" id="heading">Hola, soy CatIA!!!</h1>
                <!-- Párrafo que se animará - inicialmente oculto -->
                <p class="parrafos split-text" id="parrafo" style="display: none;">
                    CatIA es una plataforma que te permite llevar tu información financiera. <br>
                    Podrás tener un control total de tus finanzas, con un análisis de tus gastos y ahorros.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* Clase para definir la altura del contenedor principal */
    .alto{
        height: 40vh; /* 40% de la altura de la ventana */
    }
    /* Estilos para la imagen del gato */
    .img-data{
        width: 50%; /* La imagen ocupa 50% del ancho de su contenedor */
        /* Efecto de sombra blanca alrededor de la imagen */
        filter: drop-shadow(0px 0px 30px rgba(255, 255, 255, 1));
    }
    /* Media query para dispositivos móviles (pantallas menores a 777px) */
    @media (max-width: 777px) {
        .alto{
            height: 80vh; /* En móviles ocupa 80% de la altura */
            flex-direction: column!important; /* Cambia a disposición vertical */
        }
        .gato{
            width: 90%!important; /* El contenedor del gato ocupa 90% del ancho */
            display:flex;
            justify-content: center!important; /* Centra horizontalmente */
            align-items: last baseline!important; /* Alinea al final de la línea base */
            height: 40vh!important; /* Altura fija de 40% de la ventana */
        }
        .dialog{
            width: 90%!important; /* El diálogo ocupa 90% del ancho */
            padding: 10px!important; /* Padding reducido */
            display: flex!important;
            align-items: center!important; /* Centra verticalmente */
        }
    }
</style>