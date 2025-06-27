<?php

namespace App\Http\Controllers;

// Importamos las clases necesarias para el controlador
use Illuminate\Http\Request;           // Para manejar las peticiones HTTP
use Illuminate\Support\Facades\Auth;  // Para la autenticación de usuarios
use Illuminate\Validation\ValidationException;  // Para manejar errores de validación
use App\Models\User;                  // Modelo de usuario

class LoginController extends Controller
{
    /**
     * Método para autenticar usuarios y generar token de acceso
     * @param Request $request - Petición HTTP con credenciales
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con token y datos del usuario
     */
    public function login(Request $request)
    {
        try {
            // Validamos que los campos requeridos estén presentes y sean válidos
            $request->validate([
                'email' => 'required|email',      // Email es obligatorio y debe tener formato válido
                'password' => 'required|string',  // Contraseña es obligatoria y debe ser string
            ]);

            // Intentamos autenticar al usuario con las credenciales proporcionadas
            if (!Auth::attempt($request->only('email', 'password'))) {
                // Si la autenticación falla, retornamos error 401 (No autorizado)
                return response()->json([
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }

            // Si la autenticación es exitosa, buscamos el usuario en la base de datos
            $user = User::where('email', $request->email)->first();

            // Generamos un token de acceso personal para el usuario
            $token = $user->createToken('auth-token')->plainTextToken;

            // Retornamos respuesta exitosa con el token y datos del usuario
            return response()->json([
                'token' => $token,  // Token para futuras peticiones autenticadas
                'user' => [
                    'name' => $user->name,           // Nombre del usuario
                    'email' => $user->email,         // Email del usuario
                    'id' => $user->id,               // ID único del usuario
                    'ciudad' => $user->ciudad,       // Ciudad del usuario
                    'billeteras' => $user->billeteras,     // Billeteras asociadas al usuario
                    'movimientos' => $user->movimientos    // Movimientos asociados al usuario
                ]
            ], 200);  // Código de estado HTTP 200 (OK)

        } catch (ValidationException $e) {
            // Si hay errores de validación, los capturamos y retornamos
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()  // Detalles específicos de los errores de validación
            ], 422);  // Código de estado HTTP 422 (Unprocessable Entity)
        } catch (\Exception $e) {
            // Si ocurre cualquier otro error no esperado
            return response()->json([
                'message' => 'Error al iniciar sesión',
                'error' => $e->getMessage()  // Mensaje del error para debugging
            ], 500);  // Código de estado HTTP 500 (Internal Server Error)
        }
    }

    /**
     * Método para cerrar sesión del usuario eliminando su token de acceso
     * @param Request $request - Petición HTTP (debe incluir token de autenticación)
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON confirmando el logout
     */
    public function logout(Request $request)
    {
        try {
            // Eliminamos el token de acceso actual del usuario
            $request->user()->currentAccessToken()->delete();

            // Retornamos confirmación de logout exitoso
            return response()->json([
                'message' => 'Sesión cerrada correctamente'
            ], 200);  // Código de estado HTTP 200 (OK)
        } catch (\Exception $e) {
            // Si ocurre algún error durante el logout
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()  // Mensaje del error para debugging
            ], 500);  // Código de estado HTTP 500 (Internal Server Error)
        }
    }
}
