// URL base de la API en producción (servidor remoto)
// const API_URL = 'https://catia.solwebco.com/api';
// URL base de la API para desarrollo local (comentada)
const API_URL = 'http://127.0.0.1:8000/api';

// Objeto que contiene todos los métodos para interactuar con la API
export const apiService = {
    // Método para registrar un nuevo usuario
    async register(userData) {
        try {
            // Realiza una petición POST al endpoint de registro
            const response = await fetch(`${API_URL}/register`, {
                method: 'POST', // Método HTTP POST para enviar datos
                headers: {
                    'Content-Type': 'application/json' // Indica que enviamos datos en formato JSON
                },
                body: JSON.stringify(userData) // Convierte los datos del usuario a formato JSON
            });

            // Convierte la respuesta del servidor a formato JSON
            const data = await response.json();

            // Verifica si la respuesta fue exitosa (código 200-299)
            if (!response.ok) {
                // Si no fue exitosa, lanza un error con el código de estado y los datos
                throw {
                    status: response.status, // Código de estado HTTP (ej: 400, 401, 500)
                    data: data // Datos de error devueltos por el servidor
                };
            }

            // Si todo salió bien, retorna los datos de la respuesta
            return data;
        } catch (error) {
            // Captura cualquier error (de red, JSON, etc.) y lo re-lanza
            throw error;
        }
    },

    // Método para iniciar sesión de un usuario
    async login(userData) {
        try {
            // Realiza una petición POST al endpoint de login
            const response = await fetch(`${API_URL}/login`, {
                method: 'POST', // Método HTTP POST para enviar credenciales
                headers: {
                    'Content-Type': 'application/json' // Indica que enviamos datos en formato JSON
                },
                body: JSON.stringify(userData) // Convierte las credenciales a formato JSON
            });

            // Convierte la respuesta del servidor a formato JSON
            const data = await response.json();

            // Verifica si la respuesta fue exitosa (código 200-299)
            if (!response.ok) {
                // Si no fue exitosa, lanza un error con el código de estado y los datos
                throw {
                    status: response.status, // Código de estado HTTP (ej: 401 para credenciales inválidas)
                    data: data // Datos de error devueltos por el servidor
                };
            }

            // Si todo salió bien, retorna los datos de la respuesta (incluyendo el token)
            return data;
        } catch (error) {
            // Captura cualquier error (de red, JSON, etc.) y lo re-lanza
            throw error;
        }
    }
};
