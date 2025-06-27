// Importa el archivo bootstrap.js que contiene configuraciones iniciales de la aplicación
// como la configuración de Axios, CSRF tokens, y otras utilidades de Laravel
import './bootstrap';

// Importa la función createApp de Vue.js 3, que es el método principal para crear una instancia de la aplicación Vue
import { createApp } from 'vue';

// Importa la configuración del router (sistema de navegación) desde el archivo index.js en la carpeta router
// Esto permite la navegación entre diferentes páginas/vistas de la aplicación
import router from './router';

// Importa el componente principal App.vue que actúa como el contenedor raíz de toda la aplicación
// Este componente contiene la estructura base y el layout principal
import App from './App.vue';

// Importa los estilos CSS personalizados desde el archivo estilos.css
// Estos estilos se aplicarán globalmente a toda la aplicación
import '../css/estilos.css';

// Crea la instancia de la aplicación Vue y la configura:
// 1. createApp(App) - Crea una nueva instancia de Vue usando el componente App como raíz
// 2. .use(router) - Registra el router en la aplicación para habilitar la navegación
// 3. .mount('#app') - Monta la aplicación en el elemento HTML con el id "app"
//    (este elemento debe existir en el archivo blade principal)
createApp(App).use(router).mount('#app');