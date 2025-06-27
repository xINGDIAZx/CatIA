// URL base de la API para las peticiones de datos del usuario
// const API_URL = 'https://catia.solwebco.com/api';
// URL alternativa para desarrollo local (comentada)
const API_URL = 'http://127.0.0.1:8000/api';

// Servicio para manejar todas las operaciones relacionadas con datos del usuario
export const dataService = {

    // Función para obtener datos del usuario autenticado
    async getDataUser(userData) {
        try {
            // Obtiene el token de autenticación del localStorage
            const token = localStorage.getItem('token');

            // Realiza una petición POST para obtener datos del usuario
            const response = await fetch(`${API_URL}/dataUser`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Authorization': `Bearer ${token}` // Incluye el token de autenticación
                },
                body: JSON.stringify(userData) // Convierte los datos del usuario a JSON
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

    // Función para refrescar/actualizar datos del usuario
    async refreshUser(userData) {
        try {
            // Obtiene el token de autenticación del localStorage
            const token = localStorage.getItem('token');

            // Realiza una petición POST para refrescar datos del usuario
            const response = await fetch(`${API_URL}/refreshUser`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Authorization': `Bearer ${token}` // Incluye el token de autenticación
                },
                body: JSON.stringify(userData) // Convierte los datos del usuario a JSON
            });

            // Convierte la respuesta a formato JSON
            const data = await response.json();

            // Retorna los datos (no verifica response.ok para esta función)
            return data;
        } catch (error) {
            // Captura cualquier error y lo re-lanza para manejarlo en el componente
            throw error;
        }
    },

    // Función para actualizar datos del usuario
    async updateUserData(userData) {
        try {
            // Obtiene el token de autenticación del localStorage
            const token = localStorage.getItem('token');

            // Realiza una petición POST para actualizar datos del usuario
            const response = await fetch(`${API_URL}/updateUser`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Authorization': `Bearer ${token}` // Incluye el token de autenticación
                },
                body: JSON.stringify(userData) // Convierte los datos del usuario a JSON
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

    // Función para agregar una nueva billetera
    async addWallet(walletData) {
        try {
            // Obtiene el token de autenticación del localStorage
            const token = localStorage.getItem('token');

            // Realiza una petición POST para agregar una billetera
            const response = await fetch(`${API_URL}/addWallet`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Authorization': `Bearer ${token}` // Incluye el token de autenticación
                },
                body: JSON.stringify(walletData) // Convierte los datos de la billetera a JSON
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

    // Función para obtener todas las billeteras del usuario
    async getWallets(walletData) {
        try {
            // Obtiene el token de autenticación del localStorage
            const token = localStorage.getItem('token');

            // Realiza una petición POST para obtener las billeteras
            const response = await fetch(`${API_URL}/getWallets`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Authorization': `Bearer ${token}` // Incluye el token de autenticación
                },
                body: JSON.stringify(walletData) // Convierte los datos de filtro a JSON
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

    // Función para eliminar una billetera
    async deleteWallet(walletData) {
        try {
            // Obtiene el token de autenticación del localStorage
            const token = localStorage.getItem('token');

            // Realiza una petición POST para eliminar una billetera
            const response = await fetch(`${API_URL}/deleteWallet`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Authorization': `Bearer ${token}` // Incluye el token de autenticación
                },
                body: JSON.stringify(walletData) // Convierte los datos de la billetera a JSON
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

    // Función para obtener movimientos de una billetera específica
    async getMovementsbyWallet(movementData) {
        try {
            // Obtiene el token de autenticación del localStorage
            const token = localStorage.getItem('token');

            // Realiza una petición POST para obtener movimientos de una billetera
            const response = await fetch(`${API_URL}/getMovementsbyWallet`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Authorization': `Bearer ${token}` // Incluye el token de autenticación
                },
                body: JSON.stringify(movementData) // Convierte los datos de filtro a JSON
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

    // Función para agregar un nuevo movimiento a una billetera
    async addMovementsbyWallet(movementData) {
        try {
            // Obtiene el token de autenticación del localStorage
            const token = localStorage.getItem('token');

            // Realiza una petición POST para agregar un movimiento
            const response = await fetch(`${API_URL}/addMovementsbyWallet`, {
                method: 'POST', // Método HTTP POST
                headers: {
                    'Content-Type': 'application/json', // Indica que enviamos datos JSON
                    'Authorization': `Bearer ${token}` // Incluye el token de autenticación
                },
                body: JSON.stringify(movementData) // Convierte los datos del movimiento a JSON
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
