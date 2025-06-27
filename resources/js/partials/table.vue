<script setup>
// Importamos las funciones necesarias de Vue.js
// ref: para crear variables reactivas
// computed: para crear propiedades computadas que se actualizan automáticamente
// watch: para observar cambios en variables y ejecutar código cuando cambien
import { ref, computed, watch } from 'vue';

// Definimos las propiedades que recibirá este componente desde el componente padre
// defineProps es una función de Vue 3 Composition API
const props = defineProps({
    movements: {
        type: Array,        // Tipo de dato: Array de movimientos
        required: true      // Es obligatorio recibir esta propiedad
    }
});

// Imprimimos en consola los movimientos recibidos para debugging
// Útil para verificar que los datos llegan correctamente
console.log(props.movements);

// Obtenemos el mes actual en formato YYYY-MM (ejemplo: "2024-01")
// new Date() crea una fecha actual
// toISOString() la convierte a string ISO
// slice(0, 7) toma solo los primeros 7 caracteres (YYYY-MM)
const currentMonth = new Date().toISOString().slice(0, 7);

// Creamos una referencia reactiva para el mes seleccionado
// ref() hace que la variable sea reactiva (Vue la observa para cambios)
// Inicializada con el mes actual
const selectedMonth = ref(currentMonth);

// Computed property que filtra los movimientos por el mes seleccionado
// Se ejecuta automáticamente cuando cambia selectedMonth o props.movements
const filteredMovements = computed(() => {
    return props.movements.filter(movement =>
        // Para cada movimiento, comparamos su mes con el mes seleccionado
        // movement.mes debe estar en formato YYYY-MM
        // Convertimos a Date y luego a string ISO para comparar formatos
        new Date(movement.mes).toISOString().slice(0, 7) === selectedMonth.value
    );
});

// Computed property que obtiene los meses únicos disponibles para el selector
// Se ejecuta automáticamente cuando cambia props.movements
const uniqueMonths = computed(() => {
    // Extrae todos los meses de los movimientos usando map()
    // new Set() elimina duplicados automáticamente
    // [...new Set()] convierte el Set de vuelta a Array
    const months = [...new Set(props.movements.map(m => m.mes))];

    // Ordena los meses de más reciente a más antiguo
    // sort() ordena alfabéticamente (funciona bien con formato YYYY-MM)
    // reverse() invierte el orden para mostrar el más reciente primero
    return months.sort().reverse();
});

// Configuración de paginación
const itemsPerPage = 5;        // Número de elementos que se muestran por página
const currentPage = ref(1);    // Página actual (inicia en la página 1)

// Computed property que obtiene solo los movimientos de la página actual
// Se ejecuta automáticamente cuando cambia currentPage o filteredMovements
const paginatedMovements = computed(() => {
    // Calcula el índice de inicio de la página actual
    // Ejemplo: página 1 = (1-1)*5 = 0, página 2 = (2-1)*5 = 5
    const start = (currentPage.value - 1) * itemsPerPage;

    // Calcula el índice de fin de la página actual
    // Ejemplo: página 1 = 0+5 = 5, página 2 = 5+5 = 10
    const end = start + itemsPerPage;

    // Retorna solo los elementos de la página actual usando slice()
    return filteredMovements.value.slice(start, end);
});

// Computed property que calcula el número total de páginas necesarias
// Se ejecuta automáticamente cuando cambia filteredMovements
const totalPages = computed(() => {
    // Math.ceil() redondea hacia arriba para asegurar que quepan todos los elementos
    // Ejemplo: 12 elementos / 5 por página = 2.4 → 3 páginas
    return Math.ceil(filteredMovements.value.length / itemsPerPage);
});

// Función para cambiar de página
// Se llama cuando el usuario hace clic en los botones de paginación
const changePage = (page) => {
    currentPage.value = page;  // Actualiza la página actual
};

// Watcher que resetea la página a 1 cuando cambia el mes seleccionado
// Esto evita que el usuario esté en una página que no existe para el nuevo mes
watch(selectedMonth, () => {
    currentPage.value = 1;     // Vuelve a la primera página
});
</script>

<template>
    <!-- Selector de mes - Permite al usuario elegir qué mes ver -->
    <div class="mb-3">
        <!-- Dropdown (select) vinculado a selectedMonth con v-model -->
        <!-- Las clases CSS dan estilo al selector -->
        <select v-model="selectedMonth" class="form-select border-amarillo parrafos rounded-4">
            <!-- Genera una opción por cada mes disponible -->
            <!-- v-for itera sobre uniqueMonths -->
            <!-- :key es requerido por Vue para optimizar el renderizado -->
            <!-- :value asigna el valor de la opción -->
            <option v-for="month in uniqueMonths" :key="month" :value="month">
                <!-- Convierte el formato YYYY-MM a un formato legible en español -->
                <!-- Number(month.slice(0, 4)) extrae el año (primeros 4 caracteres) -->
                <!-- Number(month.slice(5, 7)) extrae el mes (caracteres 6 y 7) -->
                <!-- -1 porque los meses en JavaScript van de 0-11 -->
                <!-- toLocaleDateString() formatea la fecha en español colombiano -->
                {{ new Date(Number(month.slice(0, 4)), Number(month.slice(5, 7)) - 1, 1).toLocaleDateString('es-CO', { year: 'numeric', month: 'long' }) }}
            </option>
        </select>
    </div>

    <!-- Tabla principal que muestra los movimientos -->
    <!-- Las clases CSS dan estilo a la tabla -->
    <table class="table border-amarillo table-striped parrafos rounded-4 mb-0 align-middle">
        <!-- Encabezado de la tabla -->
        <thead>
            <tr>
                <th scope="col"></th>                    <!-- Columna vacía para iconos -->
                <th scope="col">Detalle Ingreso</th>     <!-- Columna para detalles de ingresos -->
                <th scope="col">Ingreso</th>             <!-- Columna para montos de ingresos -->
                <th scope="col">Detalle Gasto</th>       <!-- Columna para detalles de gastos -->
                <th scope="col">Gasto</th>               <!-- Columna para montos de gastos -->
            </tr>
        </thead>
        <!-- Cuerpo de la tabla -->
        <tbody>
            <!-- Itera sobre los movimientos de la página actual -->
            <!-- v-for itera sobre paginatedMovements -->
            <!-- :key es requerido por Vue para optimizar el renderizado -->
            <tr v-for="movement in paginatedMovements" :key="movement.id">
                <!-- Primera columna: Iconos según el tipo de movimiento -->
                <th scope="row">
                    <!-- Muestra icono de flecha arriba si es un ingreso -->
                    <!-- v-if solo renderiza la imagen si movement.ingreso es verdadero -->
                    <img src="../public/up.png" alt="up" width="30" v-if="movement.ingreso">

                    <!-- Muestra icono de flecha abajo si es un gasto -->
                    <!-- v-if solo renderiza la imagen si movement.gasto es verdadero -->
                    <img src="../public/down.png" alt="down" width="30" v-if="movement.gasto">
                </th>

                <!-- Columna de detalle de ingreso -->
                <td class="fs-6">
                    <!-- Muestra el detalle del ingreso -->
                    {{ movement.detalle_ingreso }} <br>

                    <!-- Muestra la fecha solo si hay detalle de ingreso -->
                    <!-- Operador ternario: si hay detalle, muestra la fecha formateada -->
                    <!-- new Date() convierte la fecha a objeto Date -->
                    <!-- toLocaleDateString() la formatea en español colombiano -->
                    {{ movement.detalle_ingreso ? new Date(movement.created_at).toLocaleDateString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '' }}
                </td>

                <!-- Columna de monto de ingreso -->
                <td class="fs-6">
                    <!-- Formatea el monto como moneda colombiana si existe -->
                    <!-- Number() convierte el string a número -->
                    <!-- toLocaleString() formatea como moneda colombiana -->
                    <!-- maximumFractionDigits: 0 elimina decimales -->
                    {{ movement.ingreso ? Number(movement.ingreso).toLocaleString('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }) + ' COP' : '' }}
                </td>

                <!-- Columna de detalle de gasto -->
                <td class="fs-6">
                    <!-- Muestra el detalle del gasto -->
                    {{ movement.detalle_gasto }} <br>

                    <!-- Muestra la fecha solo si hay detalle de gasto -->
                    <!-- Misma lógica que para ingresos -->
                    {{ movement.detalle_gasto ? new Date(movement.created_at).toLocaleDateString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '' }}
                </td>

                <!-- Columna de monto de gasto -->
                <td class="fs-6">
                    <!-- Formatea el monto como moneda colombiana si existe -->
                    <!-- Misma lógica que para ingresos -->
                    {{ movement.gasto ? Number(movement.gasto).toLocaleString('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }) + ' COP' : '' }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Navegación de paginación -->
    <!-- v-if solo muestra la paginación si hay más de una página -->
    <nav v-if="totalPages > 1" class="mt-3">
        <!-- Lista de botones de paginación -->
        <ul class="pagination justify-content-center parrafos">
            <!-- Botón "Anterior" (doble flecha izquierda) -->
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <!-- :class aplica la clase 'disabled' si estamos en la primera página -->
                <!-- @click.prevent evita que el enlace navegue y ejecuta la función -->
                <a class="btn text-white border-amarillo parrafos rounded-4" href="#" @click.prevent="changePage(currentPage - 1)">
                    <!-- Icono de doble flecha izquierda -->
                    <i class="las la-angle-double-left"></i>
                </a>
            </li>

            <!-- Números de página -->
            <!-- v-for genera un botón por cada página -->
            <li v-for="page in totalPages" :key="page" class="page-item mx-1" :class="{ active: currentPage === page }">
                <!-- :class aplica la clase 'active' si es la página actual -->
                <!-- @click.prevent ejecuta changePage con el número de página -->
                <a class="btn text-white border-amarillo parrafos rounded-4" href="#" @click.prevent="changePage(page)">{{ page }}</a>
            </li>

            <!-- Botón "Siguiente" (doble flecha derecha) -->
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <!-- :class aplica la clase 'disabled' si estamos en la última página -->
                <!-- @click.prevent ejecuta changePage con la siguiente página -->
                <a class="btn text-white border-amarillo parrafos rounded-4" href="#" @click.prevent="changePage(currentPage + 1)">
                    <!-- Icono de doble flecha derecha -->
                    <i class="las la-angle-double-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
    /* Estilos CSS específicos para este componente */
    /* scoped hace que estos estilos solo apliquen a este componente */

    /* Estilos para la página activa en la paginación */
    .page-item.active .btn {
        background-color: #79ce44;    /* Color de fondo verde */
        border-color: #8de756;        /* Color del borde verde claro */
    }

    /* Efecto hover para la página activa */
    /* Se ejecuta cuando el usuario pasa el mouse sobre el botón */
    .page-item.active .btn:hover {
        background-color: #006400;    /* Verde más oscuro al pasar el mouse */
        border-color: #006400;        /* Borde verde oscuro al pasar el mouse */
    }
</style>