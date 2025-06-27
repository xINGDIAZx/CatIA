<script setup>
    // ===== IMPORTS =====
    // Importa funciones reactivas y ciclo de vida de Vue 3
    import { onMounted, ref } from 'vue';
    // Importa el servicio para manejo de datos (API calls)
    import { dataService } from '../services/dataService';
    // Importa el servicio de IA Catia para obtener opiniones
    import { catiaService } from '../services/catiaService';
    // Importa el componente tabla que muestra los movimientos
    import TableMovements from '../partials/table.vue';
    // Importa el componente que muestra las opiniones de Catia
    import Opinion from '../partials/opinion.vue';

    // ===== VARIABLES REACTIVAS =====
    // Array reactivo que almacena todas las billeteras del usuario
    const misbilleteras = ref([]);
    // Variable reactiva que almacena la billetera actualmente seleccionada (null si no hay ninguna)
    const selectedBilletera = ref(null);
    // Array reactivo que almacena los movimientos de la billetera seleccionada
    const movements = ref([]);

    // Objeto reactivo que maneja el formulario para agregar nuevos movimientos
    const form = ref({
        tipo: '',        // Tipo de movimiento: 'Ingreso' o 'Gasto'
        monto: '',       // Cantidad del movimiento (número)
        fecha: '',       // Fecha del movimiento (formato date)
        detalle: '',     // Descripción del movimiento (texto)
        monto_total: ''  // Campo no utilizado actualmente
    });

    // ===== DATOS PARA CATIA (IA) =====
    // Objeto que prepara los datos para enviar a Catia y obtener una opinión
    let dataOpinion = {
        datos_del_usuario: {
            nombre_usuario : JSON.parse(localStorage.getItem('user')).name, // Obtiene el nombre del usuario desde localStorage
            billetera_antes_de_movimientos: null, // Saldo antes del movimiento
            ingresos: [], // Array que almacena los ingresos para análisis
            gastos: [],   // Array que almacena los gastos para análisis
            billetera_despues_de_movimientos: null // Saldo después del movimiento
        }
    };

    // ===== VALIDACIÓN DE FORMULARIO =====
    // Objeto reactivo que almacena los mensajes de error para cada campo del formulario
    const formErrors = ref({
        tipo: '',    // Error para el campo tipo
        monto: '',   // Error para el campo monto
        fecha: '',   // Error para el campo fecha
        detalle: ''  // Error para el campo detalle
    });

    // Función que valida todos los campos del formulario antes de enviar
    const validateForm = () => {
        let isValid = true; // Bandera que indica si el formulario es válido

        // Reinicia todos los errores a vacío
        formErrors.value = {
            tipo: '',
            monto: '',
            fecha: '',
            detalle: ''
        };

        // Validación campo por campo - si algún campo está vacío, marca error
        if (!form.value.tipo) {
            formErrors.value.tipo = 'El tipo es obligatorio';
            isValid = false;
        }
        if (!form.value.monto) {
            formErrors.value.monto = 'El monto es obligatorio';
            isValid = false;
        }
        if (!form.value.fecha) {
            formErrors.value.fecha = 'La fecha es obligatoria';
            isValid = false;
        }
        if (!form.value.detalle) {
            formErrors.value.detalle = 'El detalle es obligatorio';
            isValid = false;
        }

        return isValid; // Retorna true si todos los campos son válidos
    };

    // ===== FUNCIÓN PRINCIPAL - MANEJO DEL FORMULARIO =====
    // Función asíncrona que se ejecuta cuando se envía el formulario
    const handleSubmit = async (e) => {
        e.preventDefault(); // Previene el comportamiento por defecto del formulario (recargar página)

        // Valida el formulario - si no es válido, sale de la función
        if (!validateForm()) {
            return;
        }

        try {
            // Convierte la fecha del formulario a formato YYYY-MM para el backend
            const fecha = new Date(form.value.fecha);
            const mesFormateado = `${fecha.getFullYear()}-${String(fecha.getMonth() + 1).padStart(2, '0')}`;

            // Llama al servicio para agregar el movimiento a la base de datos
            await dataService.addMovementsbyWallet({
                wallet_id: selectedBilletera.value.id, // ID de la billetera seleccionada
                tipo: form.value.tipo,                 // Tipo de movimiento
                monto: form.value.monto,               // Monto del movimiento
                mes: mesFormateado,                    // Mes formateado (YYYY-MM)
                detalle: form.value.detalle,           // Detalle del movimiento
                monto_total: misbilleteras.value.monto // Monto total (parece incorrecto)
            });

            // ===== PREPARACIÓN DE DATOS PARA CATIA =====
            // Establece el saldo antes del movimiento
            dataOpinion.datos_del_usuario.billetera_antes_de_movimientos = selectedBilletera.value.monto;

            // Dependiendo del tipo de movimiento, actualiza los datos para Catia
            if (form.value.tipo === 'Ingreso') {
                // Si es ingreso, agrega a la lista de ingresos
                dataOpinion.datos_del_usuario.ingresos.push({
                    ingreso: form.value.monto,
                    detalle_ingreso: form.value.detalle
                });
                // Calcula el nuevo saldo sumando el ingreso
                dataOpinion.datos_del_usuario.billetera_despues_de_movimientos = selectedBilletera.value.monto + form.value.monto;
            } else {
                // Si es gasto, agrega a la lista de gastos
                dataOpinion.datos_del_usuario.gastos.push({
                    gasto: form.value.monto,
                    detalle_gasto: form.value.detalle
                });
                // Calcula el nuevo saldo restando el gasto
                dataOpinion.datos_del_usuario.billetera_despues_de_movimientos = selectedBilletera.value.monto - form.value.monto;
            }

            // ===== LIMPIEZA Y ACTUALIZACIÓN =====
            // Limpia el formulario después del envío exitoso
            form.value = {
                tipo: '',
                monto: '',
                fecha: '',
                detalle: ''
            };

            // Actualiza la lista de movimientos con el nuevo movimiento agregado
            movements.value = await dataService.getMovementsbyWallet({ wallet_id: selectedBilletera.value.id });

            // Actualiza los datos de las billeteras del usuario
            await refreshBilletera();

            // ===== INTEGRACIÓN CON CATIA (IA) =====
            // Llama al servicio de Catia para obtener una opinión sobre el movimiento
            const opinion = await catiaService.opinionCatia(dataOpinion);

            // Dispara un evento personalizado con la opinión de Catia para que otros componentes la escuchen
            window.dispatchEvent(new CustomEvent('opinion', {
                detail: {
                    opinion: opinion.response
                }
            }));

            // Reinicia el objeto dataOpinion para futuros movimientos
            dataOpinion = {
                datos_del_usuario: {
                    nombre_usuario : JSON.parse(localStorage.getItem('user')).name,
                    billetera_antes_de_movimientos: null,
                    ingresos: [],
                    gastos: [],
                    billetera_despues_de_movimientos: null
                }
            }

            // Actualiza la billetera seleccionada con los datos frescos
            selectedBilletera.value = misbilleteras.value.find(b => b.id === selectedBilletera.value.id);
        } catch (error) {
            // Manejo de errores - muestra el error en la consola
            console.error('Error al agregar movimiento:', error);
        }
    };

    // ===== FUNCIÓN PARA SELECCIONAR BILLETERA =====
    // Función asíncrona que se ejecuta cuando el usuario hace clic en una billetera
    const selectBilletera = async (billetera) => {
        selectedBilletera.value = billetera; // Establece la billetera seleccionada
        // Carga los movimientos de la billetera seleccionada desde la base de datos
        movements.value = await dataService.getMovementsbyWallet({ wallet_id: selectedBilletera.value.id });
    }

    // ===== CICLO DE VIDA DEL COMPONENTE =====
    // Hook que se ejecuta cuando el componente se monta (carga inicial)
    onMounted(async () => {
        await refreshBilletera(); // Carga las billeteras del usuario al iniciar
    });

    // ===== FUNCIÓN PARA REFRESCAR DATOS =====
    // Función asíncrona que actualiza los datos de las billeteras del usuario
    const refreshBilletera = async () => {
        // Obtiene los datos actualizados del usuario desde el backend
        const res = await dataService.refreshUser({ user_id: JSON.parse(localStorage.getItem('user')).id });
        misbilleteras.value = res.user.billeteras; // Actualiza la lista de billeteras
    }
</script>

<template>
    <!-- ===== CONTENEDOR PRINCIPAL ===== -->
    <div class="container mt-4">
        <!-- ===== SECCIÓN DE SELECCIÓN DE BILLETERAS ===== -->
        <!-- Se muestra cuando no hay billetera seleccionada -->
        <div class="row" v-if="!selectedBilletera">
            <div class="col-12 d-flex justify-content-center">
                <!-- Itera sobre todas las billeteras del usuario -->
                <div class="card rounded-4 me-2" v-for="billetera in misbilleteras" :key="billetera.id" @click="selectBilletera(billetera)">
                    <div class="card-body d-flex flex-column align-items-center">
                        <!-- Icono de billetera -->
                        <img src="../public/btn1.png" alt="Billetera" width="50">
                        <!-- Nombre de la billetera -->
                        <p class="parrafo">{{ billetera.nombre }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== SECCIÓN DE BILLETERA SELECCIONADA ===== -->
        <!-- Se muestra cuando hay una billetera seleccionada -->
        <div class="row" v-if="selectedBilletera">
            <div class="col-12 position-relative border-2 border-amarillo rounded-4 p-3">
                <!-- Nombre de la billetera seleccionada -->
                <h5 class="titulos fs-3 text-center">{{ selectedBilletera.nombre }}</h5>
                <!-- Descripción de la billetera -->
                <p class="parrafos text-center">{{ selectedBilletera.descripcion }}</p>
                <div class="w-100 d-flex justify-content-center mt-2">
                    <!-- Saldo actual formateado en pesos colombianos -->
                    <span class="parrafos bg-warning rounded-pill text-dark fs-4 p-2 px-4">
                        {{ Number(selectedBilletera.monto).toLocaleString('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }) }} COP
                    </span>
                </div>
                <!-- Botón para cerrar/deseleccionar la billetera -->
                <button class="btn btn-danger rounded-4 position-absolute top-0 end-0 m-1" @click="selectBilletera(null)">
                    <i class="las la-times"></i>
                </button>
            </div>
        </div>

        <!-- ===== SECCIÓN PRINCIPAL - FORMULARIO Y TABLA ===== -->
        <!-- Se muestra cuando hay una billetera seleccionada -->
        <div class="row mt-3" v-if="selectedBilletera">
            <!-- ===== COLUMNA DEL FORMULARIO ===== -->
            <div class="col-12 col-sm-12 col-md-4 p-0 mb-3">
                <form class="border-2 border-amarillo rounded-4 p-3">
                    <!-- Título del formulario -->
                    <h2 class="text-center titulos fs-4 mb-4">
                        Agregar Movimiento
                    </h2>

                    <!-- ===== CAMPO TIPO DE MOVIMIENTO ===== -->
                    <label class="form-label parrafos">
                        Tipo
                        <!-- Muestra error si existe -->
                        <small class="text-danger" v-if="formErrors.tipo">{{ formErrors.tipo }}</small>
                    </label>
                    <div class="input-group w-100 mb-3">
                        <!-- Select para elegir entre Ingreso o Gasto -->
                        <select
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="tipo"
                            placeholder="ej: Ingreso"
                            v-model="form.tipo"
                            required
                        >
                            <option value="Ingreso">Ingreso</option>
                            <option value="Gasto">Gasto</option>
                        </select>
                    </div>

                    <!-- ===== CAMPO MONTO ===== -->
                    <label class="form-label parrafos">
                        Monto
                        <!-- Muestra error si existe -->
                        <small class="text-danger" v-if="formErrors.monto">{{ formErrors.monto }}</small>
                    </label>
                    <div class="input-group w-100 mb-3">
                        <!-- Input numérico para el monto -->
                        <input
                            type="number"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="monto"
                            placeholder="ej: 12000"
                            v-model="form.monto"
                            required
                        >
                    </div>

                    <!-- ===== CAMPO FECHA ===== -->
                    <label class="form-label parrafos">
                        Fecha
                        <!-- Muestra error si existe -->
                        <small class="text-danger" v-if="formErrors.fecha">{{ formErrors.fecha }}</small>
                    </label>
                    <div class="input-group w-100 mb-3">
                        <!-- Input de tipo fecha -->
                        <input
                            type="date"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="mes"
                            v-model="form.fecha"
                            required
                        >
                    </div>

                    <!-- ===== CAMPO DETALLE ===== -->
                    <label class="form-label parrafos">
                        Detalle
                        <!-- Muestra error si existe -->
                        <small class="text-danger" v-if="formErrors.detalle">{{ formErrors.detalle }}</small>
                    </label>
                    <div class="input-group w-100 mb-3">
                        <!-- Input de texto para la descripción -->
                        <input
                            type="text"
                            class="input-group-text parrafos w-100 rounded-4 izq"
                            id="detalle"
                            placeholder="ej: Comida"
                            v-model="form.detalle"
                            required
                        >
                    </div>

                    <!-- ===== BOTÓN DE GUARDAR ===== -->
                    <button type="submit" class="btn btn-save rounded-4 w-100 mt-3 parrafos" @click="handleSubmit">
                        Guardar
                    </button>
                </form>
            </div>

            <!-- ===== COLUMNA DE LA TABLA DE MOVIMIENTOS ===== -->
            <div class="col-12 col-sm-12 col-md-8 p-0 ps-3">
                <div class="border-2 border-amarillo rounded-4 p-3 table-responsive">
                    <!-- Componente tabla que muestra todos los movimientos de la billetera -->
                    <table-movements :movements="movements" />
                </div>
            </div>
        </div>
    </div>

    <!-- ===== COMPONENTE DE OPINIÓN DE CATIA ===== -->
    <!-- Componente que muestra las opiniones de la IA Catia - posicionado fijo en la parte inferior -->
    <opinion class="opinion" />
</template>

<style scoped>
    /* ===== ESTILOS CSS ===== */

    /* Efecto hover para las tarjetas de billeteras */
    .card:hover {
        background-color: #e6ffe6; /* Color de fondo verde claro al pasar el mouse */
        transform: scale(1.05); /* Escala la tarjeta al 105% */
        transition: transform 0.3s ease-in-out; /* Transición suave de 0.3 segundos */
    }

    /* Clase para alinear texto a la izquierda */
    .izq {
        text-align: left;
    }

    /* Estilos para el botón de guardar */
    .btn-save {
        background-color: #8de756; /* Color verde */
        color: rgb(0, 0, 0); /* Texto negro */
    }

    /* Efecto hover para el botón de guardar */
    .btn-save:hover {
        background-color: #006400; /* Verde oscuro al pasar el mouse */
        color: white; /* Texto blanco */
    }

    /* Posicionamiento del componente de opinión de Catia */
    .opinion {
        position: fixed; /* Posición fija en la pantalla */
        bottom: 10px; /* 10px desde la parte inferior */
    }
</style>
