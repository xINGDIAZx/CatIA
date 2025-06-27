// URL base de la API para las peticiones a Catia
// const API_URL = 'https://catia.solwebco.com/api';
// URL alternativa para desarrollo local (comentada)
const API_URL = 'http://127.0.0.1:8000/api';

// Servicio principal de Catia que maneja las interacciones con la IA
export const catiaService = {

    // Función para obtener consejos de Catia
    async getConsejo(userData) {
        try {
            // Realiza una petición POST al endpoint de consejos
            const response = await fetch(`${API_URL}/catiaConsejo`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Accept': 'application/json' // Esperamos recibir respuesta en formato JSON
                },
                body: JSON.stringify(userData), // Convierte los datos del usuario a JSON
                redirect: 'follow' // Sigue las redirecciones automáticamente
            });

            // Convierte la respuesta a formato JSON
            const data = await response.json();

            // Verifica si la respuesta fue exitosa (código 200-299)
            if (!response.ok) {
                // Si hay error, lanza una excepción con el código de estado y datos
                throw {
                    status: response.status,
                    data: data
                };
            }

            // Retorna los datos si todo salió bien
            return data;
        } catch (error) {
            // Captura cualquier error y lo re-lanza para manejarlo en el componente
            throw error;
        }
    },

    // Función para obtener opiniones de Catia (requiere autenticación)
    async opinionCatia(userData) {
        try {
            // Obtiene el token de autenticación del localStorage
            const token = localStorage.getItem('token');

            // Realiza una petición POST al endpoint principal de Catia
            const response = await fetch(`${API_URL}/catia`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Accept': 'application/json', // Esperamos recibir respuesta en formato JSON
                    'Authorization': `Bearer ${token}` // Incluye el token de autenticación
                },
                body: JSON.stringify(userData), // Convierte los datos del usuario a JSON
                redirect: 'follow' // Sigue las redirecciones automáticamente
            });

            // Convierte la respuesta a formato JSON
            const data = await response.json();

            // Verifica si la respuesta fue exitosa (código 200-299)
            if (!response.ok) {
                // Si hay error, lanza una excepción con el código de estado y datos
                throw {
                    status: response.status,
                    data: data
                };
            }

            // Retorna los datos si todo salió bien
            return data;
        } catch (error) {
            // Captura cualquier error y lo re-lanza para manejarlo en el componente
            throw error;
        }
    }

};
