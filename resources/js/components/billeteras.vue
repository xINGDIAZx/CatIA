<script setup>
    // Importaciones necesarias de Vue y librerías externas
    import { ref, onMounted } from 'vue'; // ref para reactividad, onMounted para ciclo de vida
    import { dataService } from '../services/dataService'; // Servicio para manejar datos
    import Swal from 'sweetalert2'; // Librería para alertas bonitas

    // Variables reactivas para el estado del componente
    const billeteras = ref([]); // Array que almacenará las billeteras del usuario
    const user_id = JSON.parse(localStorage.getItem('user')).id; // Obtiene el ID del usuario desde localStorage
    const loading = ref(false); // Estado de carga para mostrar/ocultar indicadores

    // Objeto reactivo para el formulario de nueva billetera
    const formData = ref({
        nombre: '', // Campo para el nombre de la billetera
        monto: '', // Campo para el monto inicial
        descripcion: '' // Campo para la descripción
    });

    // Hook que se ejecuta cuando el componente se monta en el DOM
    onMounted(async () => {
        await cargarBilleteras(); // Carga las billeteras al iniciar
    });

    // Función para cargar las billeteras del usuario desde el servidor
    const cargarBilleteras = async () => {
        const res = await dataService.getWallets({ user_id }); // Llama al servicio para obtener billeteras
        billeteras.value = res.wallets; // Actualiza el array de billeteras con la respuesta
    };

    // Función para eliminar una billetera específica
    const eliminarBilletera = async (billeteraId) => {
        // Muestra una confirmación antes de eliminar
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Estás seguro de que deseas eliminar esta billetera?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        // Si el usuario confirma la eliminación
        if (result.isConfirmed) {
            try {
                // Llama al servicio para eliminar la billetera
                await dataService.deleteWallet({ user_id, wallet_id: billeteraId });
                await refreshUser(); // Actualiza los datos del usuario
                await cargarBilleteras(); // Recarga la lista de billeteras

                // Muestra mensaje de éxito
                Swal.fire(
                    '¡Eliminado!',
                    'La billetera ha sido eliminada exitosamente.',
                    'success'
                );
            } catch (error) {
                console.error('Error al eliminar la billetera:', error);
                // Muestra mensaje de error
                Swal.fire(
                    'Error',
                    'Hubo un error al eliminar la billetera.',
                    'error'
                );
            }
        }
    };

    // Función que maneja el envío del formulario para crear nueva billetera
    const handleSubmit = async (e) => {
        e.preventDefault(); // Previene el comportamiento por defecto del formulario
        loading.value = true; // Activa el estado de carga

        try {
            // Llama al servicio para crear la nueva billetera
            await dataService.addWallet({
                user_id,
                nombre: formData.value.nombre,
                monto: formData.value.monto,
                descripcion: formData.value.descripcion
            });

            await refreshUser(); // Actualiza los datos del usuario

            // Limpiar el formulario después de crear la billetera
            formData.value = {
                nombre: '',
                monto: '',
                descripcion: ''
            };

            // Recargar las billeteras para mostrar la nueva
            await cargarBilleteras();
        } catch (error) {
            console.error('Error al crear la billetera:', error);
        } finally {
            loading.value = false; // Desactiva el estado de carga
        }
    };

    // Función para actualizar los datos del usuario en localStorage
    const refreshUser = async () => {
        const res = await dataService.refreshUser({ user_id }); // Obtiene datos actualizados del usuario
        localStorage.setItem('user', JSON.stringify(res.user)); // Guarda en localStorage
    }

</script>

<template>
    <!-- Contenedor principal del componente -->
    <div class="container">
        <!-- Título principal de la página -->
        <h1 class="titulos text-center fs-1 mb-3">Billeteras</h1>
        <!-- Descripción de la página -->
        <p class="parrafos text-center mb-5">
            Aquí puedes ver tus billeteras.
        </p>

        <!-- Sección del formulario para crear nueva billetera -->
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <!-- Formulario que se ejecuta al enviar -->
                <form @submit="handleSubmit">
                    <div class="row">
                        <!-- Campo para el nombre de la billetera -->
                        <div class="col">
                            <label class="form-label parrafos">Nombre de la billetera</label>
                            <div class="input-group w-100 mb-2">
                                <input
                                    type="text"
                                    class="input-group-text parrafos w-100 rounded-4 izq"
                                    id="nombre"
                                    v-model="formData.nombre"
                                    placeholder="Nombre"
                                    required
                                >
                            </div>
                        </div>
                        <!-- Campo para el monto inicial -->
                        <div class="col">
                            <label class="form-label parrafos">Monto Inicial</label>
                            <div class="input-group w-100 mb-2">
                                <input
                                    type="number"
                                    class="input-group-text parrafos w-100 rounded-4 izq"
                                    id="monto"
                                    v-model="formData.monto"
                                    placeholder="Monto"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    <!-- Campo para la descripción -->
                    <label class="form-label parrafos">Descripción de la billetera</label>
                    <div class="input-group w-100 mb-2">
                        <textarea
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="descripcion"
                            v-model="formData.descripcion"
                            required
                        ></textarea>
                    </div>
                    <!-- Botón para enviar el formulario -->
                    <button
                        class="btn border-1 text-white border-amarillo parrafos w-100 rounded-4"
                        type="submit"
                        :disabled="loading"> <!-- Se deshabilita durante la carga -->
                        {{ loading ? 'Guardando...' : 'Guardar' }} <!-- Texto dinámico según el estado -->
                    </button>
                </form>
            </div>
        </div>

        <!-- Sección para mostrar la lista de billeteras -->
        <div class="row d-flex justify-content-center mt-5">
            <div class="col-md-6">
                <hr class="my-4 border-amarillo border-4">
                <!-- Lista de billeteras -->
                <ul class="list-group rounded-4">
                    <!-- Itera sobre cada billetera en el array -->
                    <li class="list-group-item d-flex justify-content-between align-items-center" v-for="billetera in billeteras" :key="billetera.id">
                        <!-- Información de la billetera -->
                        <div class="w-50">
                            <span class="parrafos">{{ billetera.nombre }}</span> <!-- Muestra el nombre -->
                            <p class="parrafos text-muted fs-6">{{ billetera.descripcion }}</p> <!-- Muestra la descripción -->
                        </div>
                        <!-- Monto formateado como moneda colombiana -->
                        <span class="badge text-bg-primary rounded-pill p-2">{{ Number(billetera.monto).toLocaleString('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }) }} COP</span>
                        <!-- Botón para eliminar la billetera -->
                        <span class="badge text-bg-danger rounded-circle" style="cursor: pointer;" @click="eliminarBilletera(billetera.id)">x</span>
                    </li>
                </ul>
                <!-- Mensaje cuando no hay billeteras -->
                <div v-if="billeteras.length === 0">
                    <p class="parrafos text-center">No hay billeteras</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    /* Estilo para alinear texto a la izquierda */
    .izq {
        text-align: left;
    }
</style>