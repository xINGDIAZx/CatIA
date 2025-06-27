<script setup>
// Importamos las funciones necesarias de Vue y nuestro servicio de datos
import { ref, onMounted } from 'vue';
import { dataService } from '../services/dataService';

// Definimos el estado reactivo para almacenar los datos del usuario
// ref() crea una variable reactiva que Vue puede rastrear para cambios
const userData = ref({
    name: '',        // Nombre del usuario
    email: '',       // Email del usuario
    ciudad: ''       // Ciudad del usuario
});

// Estados para controlar la interfaz de usuario
const loading = ref(true);      // Indica si se está cargando algo
const error = ref(null);        // Almacena mensajes de error
const success = ref(null);      // Almacena mensajes de éxito

// Estados para los campos de contraseña
const currentPassword = ref('');    // Contraseña actual ingresada
const newPassword = ref('');        // Nueva contraseña ingresada
const showCurrentPassword = ref(false);  // Controla si mostrar/ocultar contraseña actual
const showNewPassword = ref(false);      // Controla si mostrar/ocultar nueva contraseña

// Función asíncrona para obtener los datos del usuario desde el servidor
const fetchUserData = async () => {
    try {
        // Obtenemos el ID del usuario desde el localStorage
        const data = {
            user_id: JSON.parse(localStorage.getItem('user')).id
        };
        // Llamamos al servicio para obtener los datos del usuario
        const res = await dataService.getDataUser(data);
        // Actualizamos el estado con los datos recibidos
        userData.value = res.user;
    } catch (err) {
        // Si hay un error, lo mostramos al usuario
        error.value = 'Error al cargar los datos del usuario';
        console.error(err);
    } finally {
        // Siempre terminamos el estado de carga
        loading.value = false;
    }
};

// Función para actualizar el perfil del usuario
const updateProfile = async (e) => {
    e.preventDefault();  // Prevenimos el comportamiento por defecto del formulario
    loading.value = true;  // Activamos el estado de carga
    error.value = null;    // Limpiamos errores anteriores
    success.value = null;  // Limpiamos mensajes de éxito anteriores

    try {
        // Preparamos los datos para enviar al servidor
        const updateData = {
            user_id: JSON.parse(localStorage.getItem('user')).id,
            name: userData.value.name,
            email: userData.value.email,
            ciudad: userData.value.ciudad
        };

        // Solo incluimos las contraseñas si el usuario las proporcionó
        if (currentPassword.value && newPassword.value) {
            updateData.current_password = currentPassword.value;
            updateData.new_password = newPassword.value;
        }

        // Enviamos los datos actualizados al servidor
        const res = await dataService.updateUserData(updateData);
        success.value = 'Perfil actualizado correctamente';
        await refreshUser();  // Actualizamos los datos en localStorage

        // Limpiamos los campos de contraseña por seguridad
        currentPassword.value = '';
        newPassword.value = '';
    } catch (err) {
        // Si hay un error, lo mostramos al usuario
        error.value = 'Error al actualizar el perfil';
        console.error(err);
    } finally {
        // Siempre terminamos el estado de carga
        loading.value = false;
    }
};

// Función para refrescar los datos del usuario en localStorage
const refreshUser = async () => {
    const res = await dataService.refreshUser({ user_id: JSON.parse(localStorage.getItem('user')).id });
    localStorage.setItem('user', JSON.stringify(res.user));
}

// Hook del ciclo de vida: se ejecuta cuando el componente se monta
onMounted(() => {
    fetchUserData();  // Cargamos los datos del usuario al iniciar
});
</script>

<template>
    <!-- Contenedor principal del componente -->
    <div class="container">
        <!-- Título principal de la página -->
        <h1 class="titulos text-center fs-1 mb-3">Perfil</h1>
        <!-- Descripción de la página -->
        <p class="parrafos text-center mb-5">
            Aquí puedes editar tu perfil y tus datos de acceso.
        </p>
        <!-- Contenedor centrado para el formulario -->
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <!-- Indicador de carga -->
                <div v-if="loading" class="text-center parrafos">
                    Cargando...
                </div>
                <!-- Mensaje de error -->
                <div v-if="error" class="alert alert-danger parrafos">
                    {{ error }}
                </div>
                <!-- Mensaje de éxito con botón para cerrar -->
                <div v-if="success" class="alert alert-success d-flex justify-content-between align-items-center">
                    <span class="parrafos">{{ success }}</span>
                    <button type="button" class="btn-close ms-2" aria-label="Cerrar" @click="success = null"></button>
                </div>
                <!-- Formulario para actualizar el perfil -->
                <form @submit="updateProfile">
                    <!-- Campo para el nombre -->
                    <label class="form-label parrafos">Nombre</label>
                    <div class="input-group w-100 mb-2">
                        <input
                            type="text"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="nombre"
                            placeholder="Nombre"
                            v-model="userData.name"
                            required
                        >
                    </div>
                    <!-- Campo para el email -->
                    <label class="form-label parrafos">Email</label>
                    <div class="input-group w-100 mb-2">
                        <input
                            type="email"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="email"
                            placeholder="Email"
                            v-model="userData.email"
                            required
                        >
                    </div>
                    <!-- Campo para la ciudad -->
                    <label class="form-label parrafos">Ciudad</label>
                    <div class="input-group w-100 mb-2">
                        <input
                            type="text"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="ciudad"
                            placeholder="Ciudad"
                            v-model="userData.ciudad"
                            required
                        >
                    </div>
                    <!-- Separador visual -->
                    <hr class="my-4 border-amarillo border-4">
                    <!-- Campo para la contraseña actual -->
                    <label class="form-label parrafos">Password Actual</label>
                    <div class="input-group w-100 mb-4 position-relative">
                        <!-- Botón para mostrar/ocultar contraseña -->
                        <kbd
                            class="bg-warning text-dark p-1 position-absolute top-0 start-100 translate-middle rounded-4"
                            role="button"
                            @click="showCurrentPassword = !showCurrentPassword"
                        >
                            <!-- Icono que cambia según el estado -->
                            <i class="las" :class="showCurrentPassword ? 'la-eye-slash' : 'la-eye'"></i>
                        </kbd>
                        <input
                            :type="showCurrentPassword ? 'text' : 'password'"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="currentPassword"
                            placeholder="Password Actual"
                            v-model="currentPassword"
                        >
                    </div>
                    <!-- Campo para la nueva contraseña -->
                    <label class="form-label parrafos">Password Nuevo</label>
                    <div class="input-group w-100 mb-4 position-relative">
                        <!-- Botón para mostrar/ocultar contraseña -->
                        <kbd
                            class="bg-warning text-dark p-1 position-absolute top-0 start-100 translate-middle rounded-4"
                            role="button"
                            @click="showNewPassword = !showNewPassword"
                        >
                            <!-- Icono que cambia según el estado -->
                            <i class="las" :class="showNewPassword ? 'la-eye-slash' : 'la-eye'"></i>
                        </kbd>
                        <input
                            :type="showNewPassword ? 'text' : 'password'"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="newPassword"
                            placeholder="Password Nuevo"
                            v-model="newPassword"
                        >
                    </div>
                    <!-- Botón para enviar el formulario -->
                    <button
                        class="btn border-1 text-white border-amarillo parrafos w-100 rounded-4"
                        type="submit"
                        :disabled="loading">  <!-- Se deshabilita durante la carga -->
                        {{ loading ? 'Guardando...' : 'Guardar' }}  <!-- Texto dinámico -->
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* Estilo para alinear el texto a la izquierda en los inputs */
    .izq {
        text-align: left;
    }
</style>