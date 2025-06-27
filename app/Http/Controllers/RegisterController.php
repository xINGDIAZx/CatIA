<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para manejar el registro de usuarios
 * Gestiona la creación de nuevas cuentas de usuario en el sistema
 */
class RegisterController extends Controller
{
    /**
     * Método para registrar un nuevo usuario
     * Valida los datos de entrada, crea el usuario y devuelve un token de autenticación
     *
     * @param Request $request - Objeto que contiene los datos del formulario de registro
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con el token y datos del usuario
     */
    public function register(Request $request)
    {
        try {
            // Validación de los campos requeridos del formulario
            $request->validate([
                'name' => 'required|string|max:255',        // Nombre obligatorio, string, máximo 255 caracteres
                'email' => 'required|email|unique:users',   // Email obligatorio, formato válido, único en tabla users
                'password' => 'required|string|min:8',      // Contraseña obligatoria, string, mínimo 8 caracteres
            ]);

            // Creación del nuevo usuario en la base de datos
            $user = User::create([
                'name' => $request->name,                   // Asigna el nombre del formulario
                'email' => $request->email,                 // Asigna el email del formulario
                'password' => Hash::make($request->password), // Encripta la contraseña antes de guardarla
            ]);

            // Genera un token de autenticación para el usuario recién creado
            $token = $user->createToken('auth-token')->plainTextToken;

            // Retorna respuesta exitosa con el token y datos del usuario
            return response()->json([
                'token' => $token,                          // Token de autenticación
                'user' => [
                    'id' => $user->id,                      // ID del usuario
                    'name' => $user->name,                  // Nombre del usuario
                    'email' => $user->email                 // Email del usuario
                ]
            ], 201); // Código HTTP 201: Created

        } catch (ValidationException $e) {
            // Manejo de errores de validación (campos faltantes, formato incorrecto, etc.)
            return response()->json([
                'message' => 'Error de validación',          // Mensaje de error general
                'errors' => $e->errors()                    // Detalles específicos de los errores de validación
            ], 422); // Código HTTP 422: Unprocessable Entity

        } catch(\Exception $e) {
            // Manejo de errores generales (problemas de base de datos, etc.)
            return response()->json([
                'message' => 'Error al crear el usuario',    // Mensaje de error general
                'error' => $e->getMessage()                 // Mensaje específico del error
            ], 500); // Código HTTP 500: Internal Server Error
        }
    }
}
