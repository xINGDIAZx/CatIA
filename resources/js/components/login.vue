<script setup>
    // Importamos las funciones necesarias de Vue Router y Vue
    import { useRouter } from 'vue-router'; // Para navegación entre páginas
    import { ref, onMounted } from 'vue'; // ref para variables reactivas, onMounted para ciclo de vida
    import { apiService } from '../services/apiService'; // Servicio para llamadas a la API

    // Instancia del router para navegación
    const router = useRouter();

    // Objeto reactivo para manejar el estado de las imágenes y visibilidad de contraseña
    const img = ref({
        op1: null, // Estado de la primera imagen (gato normal)
        op2: null, // Estado de la segunda imagen (ojo abierto)
        op3: null, // Estado de la tercera imagen (ojo cerrado)
        showPassword: false // Controla si mostrar/ocultar contraseña
    });

    // Objeto reactivo para los datos del formulario
    const form = ref({
        email: '', // Campo de email
        password: '' // Campo de contraseña
    });

    // Objeto reactivo para manejar errores de validación
    const errors = ref({
        email: '', // Error del campo email
        password: '' // Error del campo password
    });

    // Variable reactiva para controlar el estado de envío del formulario
    const isSubmitting = ref(false);
    // Variable reactiva para mensajes de éxito
    const successMessage = ref('');
    // Variable reactiva para mensajes de error
    const errorMessage = ref('');

    // Función para regresar a la página principal
    const goHome = () => {
        // Dispara un evento personalizado para notificar el inicio
        window.dispatchEvent(new CustomEvent('homeStart'));
        // Navega a la ruta principal
        router.push('/');
    }

    // Hook que se ejecuta cuando el componente se monta
    onMounted(() => {
        // Muestra la primera imagen por defecto
        img.value.op1 = true;
    });

    // Función para seleccionar qué imagen mostrar según el campo activo
    function selectCat(item){
        // Resetea todos los estados de imagen
        img.value.op1 = false;
        img.value.op2 = false;
        img.value.op3 = false;
        // Activa la imagen correspondiente según el parámetro
        img.value.op1 = item === 1 ? true : false; // Gato normal
        img.value.op2 = item === 2 ? true : false; // Ojo abierto (email)
        img.value.op3 = item === 3 ? true : false; // Ojo cerrado (password)
        // Oculta la contraseña y resetea el estado
        img.value.showPassword = false;
        document.getElementById('password').type = 'password';
    }

    // Función para mostrar/ocultar la contraseña
    function showPassword(){
        // Resetea todos los estados de imagen
        img.value.op1 = null;
        img.value.op2 = null;
        img.value.op3 = null;
        // Alterna entre mostrar y ocultar la contraseña
        if(img.value.showPassword){
            img.value.showPassword = false;
            document.getElementById('password').type = 'password';
        }else{
            img.value.showPassword = true;
            document.getElementById('password').type = 'text';
        }
    }

    // Función asíncrona para procesar el login
    const login = async () => {
        // Activa el estado de envío
        isSubmitting.value = true;
        // Limpia mensajes de error previos
        errorMessage.value = '';

        try {
            // Llama al servicio de API para autenticar
            const data = await apiService.login(form.value);

            // Guarda el token de autenticación en localStorage
            localStorage.setItem('token', data.token);
            // Guarda los datos del usuario en localStorage
            localStorage.setItem('user', JSON.stringify(data.user));

            // Redirige al dashboard después del login exitoso
            window.location.href = '/dashboard';
        } catch (error) {
            console.error(error);
            // Maneja diferentes tipos de errores
            if (error.status === 422) {
                errorMessage.value = 'Credenciales inválidas';
            } else {
                errorMessage.value = 'Error al iniciar sesión';
            }
        } finally {
            // Siempre desactiva el estado de envío al finalizar
            isSubmitting.value = false;
        }
    }
</script>

<template>
    <!-- Contenedor principal con posicionamiento relativo -->
    <div class="position-relative">
        <!-- Botón de regreso posicionado en la esquina superior izquierda -->
        <div class="position-absolute top-0 start-0">
            <button class="btn border-1 text-white border-danger m-2 rounded-4" @click="goHome">
                Atras
            </button>
        </div>
        <!-- Contenedor principal con flexbox para organizar el contenido -->
        <div class="w-100 d-flex justify-content-between align-items-center alto">
            <!-- Sección izquierda para mostrar imágenes y mensajes -->
            <div class="d-flex flex-column justify-content-center align-items-center w-75 alto gato">
                <!-- Imagen del gato normal (estado inicial) -->
                <img v-if="img.op1" src="../public/cat1.png" alt="cat1" class="img-fluid img-data mx-5">
                <!-- Imagen del ojo abierto (cuando se enfoca en email) -->
                <img v-if="img.op2" src="../public/see.png" alt="see" class="img-fluid img-data mx-5">
                <!-- Imagen del ojo cerrado (cuando se enfoca en password) -->
                <img v-if="img.op3" src="../public/hide.png" alt="hide" class="img-fluid img-data mx-5">
                <!-- Imagen de baja seguridad (cuando se muestra la contraseña) -->
                <img v-if="img.showPassword" src="../public/lowsecure.png" alt="show" class="img-fluid img-data mx-5">
                <!-- Mensaje de error si existe -->
                <span v-if="errorMessage" class="error-message parrafos text-warning text-center mt-2">{{ errorMessage }}</span>
            </div>
            <!-- Sección derecha para el formulario de login -->
            <div class="w-75 alto p-5 mt-5 dialog">
                <!-- Formulario con prevención de envío por defecto -->
                <form @submit.prevent="login">
                    <!-- Etiqueta para el campo email -->
                    <label class="form-label parrafos">Email</label>
                    <!-- Grupo de input para email -->
                    <div class="input-group w-100 mb-2">
                        <input
                            type="email"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="email"
                            placeholder="Email"
                            @focus="selectCat(2)"
                            v-model="form.email"
                            required
                        >
                    </div>
                    <!-- Etiqueta para el campo password -->
                    <label class="form-label parrafos">Password</label>
                    <!-- Grupo de input para password con botón de mostrar/ocultar -->
                    <div class="input-group w-100 mb-4 position-relative">
                        <!-- Botón para mostrar/ocultar contraseña -->
                        <kbd class="bg-warning text-dark p-1 position-absolute top-0 start-100 translate-middle rounded-4" role="button" @click="showPassword" v-if="!img.showPassword">
                            <i class="las la-eye"></i>
                        </kbd>
                        <input
                            type="password"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="password"
                            placeholder="Password"
                            @focus="selectCat(3)"
                            v-model="form.password"
                            required
                        >
                    </div>
                    <!-- Botón de envío del formulario -->
                    <button
                        class="btn border-1 text-white border-amarillo parrafos w-100 rounded-4"
                        type="submit"
                        :disabled="isSubmitting"
                    >
                        <!-- Texto dinámico según el estado de envío -->
                        {{ isSubmitting ? 'Iniciando sesión...' : 'Login' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* Estilo para definir la altura de los contenedores principales */
    .alto{
        height: 40vh;
    }
    /* Estilo para las imágenes con efecto de sombra */
    .img-data{
        width: 60%;
        filter: drop-shadow(0px 0px 30px rgba(255, 255, 255, 1));
    }
    /* Alineación de texto a la izquierda */
    .izq{
        text-align: start;
    }
    /* Media query para dispositivos móviles */
    @media (max-width: 777px) {
        /* Ajusta altura y dirección para móviles */
        .alto{
            height: 80vh;
            flex-direction: column!important;
        }
        /* Ajusta el contenedor de la imagen para móviles */
        .gato{
            width: 90%!important;
            display:flex;
            justify-content: center!important;
            align-items: last baseline!important;
            height: 60vh!important;
        }
        /* Ajusta el contenedor del formulario para móviles */
        .dialog{
            width: 90%!important;
            padding: 10px!important;
            margin-top: 0!important;
            display: flex!important;
            align-items: center!important;
        }
    }
</style>