<script setup>
    // Importaciones necesarias de Vue y Vue Router
    import { useRouter } from 'vue-router'; // Para navegación entre páginas
    import { ref, computed } from 'vue'; // Para reactividad y propiedades computadas
    import { apiService } from '../services/apiService'; // Servicio para llamadas a la API

    // Instancia del router para navegación
    const router = useRouter();

    // Función para regresar a la página principal
    const goHome = () => {
        // Dispara un evento personalizado para notificar que se va al home
        window.dispatchEvent(new CustomEvent('homeStart'));
        // Navega a la ruta principal
        router.push('/');
    }

    // Estado reactivo para controlar qué imagen mostrar (gato feliz, triste, o neutral)
    const img = ref({
        op1: true,  // Imagen inicial (gato saludando)
        op2: false, // Imagen de éxito (gato feliz)
        op3: false  // Imagen de error (gato triste)
    })

    // Estado reactivo del formulario con los campos de entrada
    const form = ref({
        name: '',     // Campo para el nombre del usuario
        email: '',    // Campo para el email del usuario
        password: ''  // Campo para la contraseña del usuario
    })

    // Estado reactivo para almacenar mensajes de error de validación
    const errors = ref({
        name: '',     // Error del campo nombre
        email: '',    // Error del campo email
        password: ''  // Error del campo contraseña
    })

    // Estados de UI para controlar la interfaz
    const isSubmitting = ref(false)      // Indica si se está enviando el formulario
    const successMessage = ref('')       // Mensaje de éxito después del registro
    const welcomeMessage = ref('')       // Mensaje de bienvenida o error

    // Función para validar el campo nombre
    const validateName = () => {
        if (!form.value.name.trim()) {
            // Si el nombre está vacío o solo tiene espacios
            errors.value.name = 'El nombre es requerido'
            img.value.op1 = false; // Oculta imagen neutral
            img.value.op3 = true;  // Muestra imagen de error
        } else if (form.value.name.trim().length < 2) {
            // Si el nombre tiene menos de 2 caracteres
            errors.value.name = 'El nombre debe tener al menos 2 caracteres'
            img.value.op1 = false; // Oculta imagen neutral
            img.value.op3 = true;  // Muestra imagen de error
        } else {
            // Si el nombre es válido
            errors.value.name = ''
            img.value.op1 = true;  // Muestra imagen neutral
            img.value.op3 = false; // Oculta imagen de error
        }
    }

    // Función para validar el campo email
    const validateEmail = () => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/ // Expresión regular para validar email
        if (!form.value.email) {
            // Si el email está vacío
            errors.value.email = 'El email es requerido'
            img.value.op1 = false; // Oculta imagen neutral
            img.value.op3 = true;  // Muestra imagen de error
        } else if (!emailRegex.test(form.value.email)) {
            // Si el email no tiene formato válido
            errors.value.email = 'Formato de email inválido'
            img.value.op1 = false; // Oculta imagen neutral
            img.value.op3 = true;  // Muestra imagen de error
        } else {
            // Si el email es válido
            errors.value.email = ''
            img.value.op1 = true;  // Muestra imagen neutral
            img.value.op3 = false; // Oculta imagen de error
        }
    }

    // Función para validar el campo contraseña
    const validatePassword = () => {
        if (!form.value.password) {
            // Si la contraseña está vacía
            errors.value.password = 'La contraseña es requerida'
            img.value.op1 = false; // Oculta imagen neutral
            img.value.op3 = true;  // Muestra imagen de error
        } else if (form.value.password.length < 8) {
            // Si la contraseña tiene menos de 8 caracteres
            errors.value.password = 'La contraseña debe tener al menos 8 caracteres'
            img.value.op1 = false; // Oculta imagen neutral
            img.value.op3 = true;  // Muestra imagen de error
        } else {
            // Si la contraseña es válida
            errors.value.password = ''
            img.value.op1 = true;  // Muestra imagen neutral
            img.value.op3 = false; // Oculta imagen de error
        }
    }

    // Propiedad computada que verifica si todo el formulario es válido
    const isValid = computed(() => {
        return form.value.name &&           // Nombre no está vacío
            form.value.email &&             // Email no está vacío
            form.value.password &&          // Contraseña no está vacía
            !errors.value.name &&           // No hay error en nombre
            !errors.value.email &&          // No hay error en email
            !errors.value.password          // No hay error en contraseña
    })

    // Función principal para registrar al usuario
    const register = async () => {
        // Ejecuta todas las validaciones antes de enviar
        validateName();
        validateEmail();
        validatePassword();

        // Si el formulario no es válido, no continúa
        if(!isValid.value){
            return;
        }

        // Activa el estado de envío
        isSubmitting.value = true;
        successMessage.value = '';

        try{
            // Llama al servicio de API para registrar al usuario
            const data = await apiService.register(form.value);

            // Si el registro es exitoso, muestra mensaje de bienvenida
            successMessage.value = '¡Cuenta creada exitosamente!'
            welcomeMessage.value = '¡Bienvenido a la aventura! <br>' + data.user.name

            // Cambia la imagen a la de éxito
            img.value.op1 = false; // Oculta imagen neutral
            img.value.op2 = true;  // Muestra imagen de éxito
            img.value.op3 = false; // Oculta imagen de error

            // Limpia el formulario después del registro exitoso
            form.value = {
                nombre: '',    // Nota: hay inconsistencia aquí (debería ser 'name')
                email: '',
                password: ''
            }

            // Limpia todos los errores
            errors.value = {
                nombre: '',    // Nota: hay inconsistencia aquí (debería ser 'name')
                email: '',
                password: ''
            }

        } catch (error) {
            // Si hay un error en el registro
            console.log(error);
            img.value.op1 = false; // Oculta imagen neutral
            img.value.op2 = false; // Oculta imagen de éxito
            img.value.op3 = true;  // Muestra imagen de error

            // Maneja diferentes tipos de errores
            if (error.status === 422) {
                // Error de validación del servidor
                if (error.data.errors.email[0] === 'validation.unique') {
                    // Email ya existe en la base de datos
                    welcomeMessage.value = '¡Error al crear el usuario! <br> El email ya está en uso'
                } else {
                    // Otros errores de validación
                    welcomeMessage.value = '¡Error al crear el usuario! <br>' + error.data.errors
                }
            }
        } finally {
            // Siempre desactiva el estado de envío al finalizar
            isSubmitting.value = false;
        }
    }
</script>

<template>
    <!-- Contenedor principal con posición relativa -->
    <div class="position-relative">
        <!-- Botón de regreso posicionado en la esquina superior izquierda -->
        <div class="position-absolute top-0 start-0">
            <button class="btn border-1 text-white border-danger m-2 rounded-4" @click="goHome">
                Atras
            </button>
        </div>

        <!-- Contenedor principal con flexbox para distribuir el contenido -->
        <div class="w-100 d-flex justify-content-between align-items-center alto">

            <!-- Sección izquierda: Imagen del gato y mensajes -->
            <div class="d-flex flex-column justify-content-center align-items-center w-75 alto gato">
                <!-- Imagen del gato que cambia según el estado -->
                <img v-if="img.op1" src="../public/hi.png" alt="cat1" class="img-fluid img-data mx-5">
                <img v-if="img.op2" src="../public/yeah.png" alt="cat1" class="img-fluid img-data mx-5">
                <img v-if="img.op3" src="../public/nop.png" alt="cat1" class="img-fluid img-data mx-5">

                <!-- Mensajes de error que se muestran condicionalmente -->
                <span v-if="errors.name" class="error-message parrafos text-warning text-center mt-2">{{ errors.name }}</span>
                <span v-if="errors.email" class="error-message parrafos text-warning text-center mt-2">{{ errors.email }}</span>
                <span v-if="errors.password" class="error-message parrafos text-warning text-center mt-2">{{ errors.password }}</span>

                <!-- Mensaje de bienvenida o error con HTML permitido -->
                <h1 class="titulos text-center mt-2 fs-3" v-html="welcomeMessage"></h1>
            </div>

            <!-- Sección derecha: Formulario de registro -->
            <div class="w-75 alto p-4 mt-3 dialog">
                <!-- Formulario que previene el envío por defecto -->
                <form @submit.prevent="register">

                    <!-- Campo para el nombre completo -->
                    <label class="form-label parrafos">Nombre Completo</label>
                    <div class="input-group w-100 mb-2">
                        <input
                            type="text"
                            class="input-group-text parrafos w-100 izq rounded-4"
                            id="name"
                            placeholder="Nombre Completo"
                            v-model="form.name"
                            :class="{'is-invalid': errors.name}"
                            @blur="validateName"
                        >
                    </div>

                    <!-- Campo para el email -->
                    <label class="form-label parrafos">Email</label>
                    <div class="input-group w-100 mb-2">
                        <input
                            type="email"
                            class="input-group-text parrafos w-100 izq rounded-4"
                            id="email"
                            placeholder="Email"
                            v-model="form.email"
                            :class="{'is-invalid': errors.email}"
                            @blur="validateEmail"
                        >
                    </div>

                    <!-- Campo para la contraseña -->
                    <label class="form-label parrafos">Password</label>
                    <div class="input-group w-100 mb-2">
                        <input
                            type="password"
                            class="input-group-text parrafos w-100 izq rounded-4"
                            id="password"
                            placeholder="Password"
                            v-model="form.password"
                            :class="{'is-invalid': errors.password}"
                            @blur="validatePassword"
                        >
                    </div>

                    <!-- Botón de envío del formulario -->
                    <button
                        class="btn border-1 text-white parrafos w-100 rounded-4"
                        type="submit"
                        :disabled="!isValid"
                        :class="{'border-rojo': !isValid, 'border-verde': isValid}"
                    >
                        <!-- Texto del botón que cambia según la validación -->
                        {{ isValid ? 'Listo para la aventura' : 'Llena el formulario' }}
                    </button>

                    <!-- Mensaje de éxito que se muestra condicionalmente -->
                    <div v-if="successMessage" class="success-message parrafos text-center mt-2">
                        {{ successMessage }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* Estilo para definir la altura de los contenedores principales */
    .alto{
        height: 40vh; /* 40% de la altura de la ventana */
    }

    /* Estilo para las imágenes del gato */
    .img-data{
        width: 60%; /* Ancho del 60% del contenedor */
        filter: drop-shadow(0px 0px 30px rgba(255, 255, 255, 1)); /* Sombra blanca brillante */
    }

    /* Estilo para alinear texto a la izquierda */
    .izq{
        text-align: start;
    }

    /* Media query para dispositivos móviles (pantallas menores a 777px) */
    @media (max-width: 777px) {
        .alto{
            height: 80vh; /* Aumenta la altura en móviles */
            flex-direction: column!important; /* Cambia la dirección del flexbox a columna */
        }
        .gato{
            width: 90%!important; /* Reduce el ancho en móviles */
            display:flex;
            justify-content: center!important; /* Centra horizontalmente */
            align-items: last baseline!important; /* Alinea al final de la línea base */
            height: 60vh!important; /* Altura específica para móviles */
        }
        .dialog{
            width: 90%!important; /* Reduce el ancho en móviles */
            padding: 0px!important; /* Elimina el padding */
            margin-top: 0!important; /* Elimina el margen superior */
            display: flex!important; /* Activa flexbox */
            align-items: center!important; /* Centra verticalmente */
        }
    }
</style>
