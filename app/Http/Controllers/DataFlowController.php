<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Movement;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para manejar el flujo de datos de la aplicación
 * Gestiona operaciones CRUD para usuarios, billeteras y movimientos financieros
 */
class DataFlowController extends Controller
{
    /**
     * Obtiene los datos básicos del usuario autenticado
     * @param Request $request - Solicitud HTTP
     * @return \Illuminate\Http\JsonResponse - Datos del usuario en formato JSON
     */
    public function dataUser(Request $request)
    {
        // Obtiene el usuario autenticado desde la solicitud
        $user = $request->user();

        // Retorna solo los datos básicos del usuario (sin información sensible)
        return response()->json([
            'user' => [
                'id' => $user->id, // ID único del usuario
                'name' => $user->name, // Nombre del usuario
                'email' => $user->email, // Email del usuario
                'ciudad' => $user->ciudad // Ciudad del usuario
            ]
        ]);
    }

    /**
     * Actualiza y obtiene todos los datos del usuario incluyendo billeteras y movimientos
     * @param Request $request - Solicitud HTTP
     * @return \Illuminate\Http\JsonResponse - Datos completos del usuario
     */
    public function refreshUser(Request $request)
    {
        // Obtiene el usuario autenticado
        $user = $request->user();

        // Retorna datos completos del usuario incluyendo relaciones
        return response()->json([
            'user' => [
                'name' => $user->name, // Nombre del usuario
                'email' => $user->email, // Email del usuario
                'id' => $user->id, // ID único del usuario
                'ciudad' => $user->ciudad, // Ciudad del usuario
                'billeteras' => $user->billeteras, // Todas las billeteras del usuario
                'movimientos' => $user->movimientos // Todos los movimientos del usuario
            ]
        ]);
    }

    /**
     * Actualiza los datos del usuario autenticado
     * @param Request $request - Solicitud HTTP con datos a actualizar
     * @return \Illuminate\Http\JsonResponse - Usuario actualizado o errores de validación
     */
    public function updateUser(Request $request)
    {
        try {
            // Valida los datos de entrada según las reglas definidas
            $request->validate([
                'name' => 'required|string|max:255', // Nombre requerido, máximo 255 caracteres
                'email' => 'required|email|unique:users,email,' . $request->user()->id, // Email único excepto para el usuario actual
                'ciudad' => 'required|string|max:255', // Ciudad requerida, máximo 255 caracteres
                'current_password' => 'nullable|required_with:new_password', // Contraseña actual solo si se va a cambiar
                'new_password' => 'nullable|required_with:current_password|min:8' // Nueva contraseña mínima 8 caracteres
            ]);

            // Obtiene el usuario autenticado
            $user = $request->user();

            // Actualiza los datos básicos del usuario
            $user->name = $request->name;
            $user->email = $request->email;
            $user->ciudad = $request->ciudad;

            // Si se proporcionaron contraseñas, valida y actualiza la contraseña
            if ($request->filled('current_password') && $request->filled('new_password')) {
                // Verifica que la contraseña actual sea correcta
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json(['error' => 'La contraseña actual es incorrecta'], 422);
                }
                // Hashea y guarda la nueva contraseña
                $user->password = Hash::make($request->new_password);
            }

            // Guarda los cambios en la base de datos
            $user->save();

            // Retorna el usuario actualizado
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'ciudad' => $user->ciudad
                ]
            ]);
        } catch (ValidationException $e) {
            // Si hay errores de validación, los retorna
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Obtiene todas las billeteras del usuario autenticado
     * @param Request $request - Solicitud HTTP
     * @return \Illuminate\Http\JsonResponse - Lista de billeteras del usuario
     */
    public function getWallets(Request $request)
    {
        // Busca todas las billeteras que pertenecen al usuario autenticado
        $wallets = Wallet::where('user_id', $request->user()->id)->get();

        // Retorna las billeteras en formato JSON
        return response()->json([
            'wallets' => $wallets
        ]);
    }

    /**
     * Crea una nueva billetera para el usuario autenticado
     * @param Request $request - Solicitud HTTP con datos de la billetera
     * @return \Illuminate\Http\JsonResponse - Billetera creada o errores de validación
     */
    public function addWallet(Request $request)
    {
        try {
            // Valida los datos de entrada para la nueva billetera
            $request->validate([
                'nombre' => 'required|string|max:255', // Nombre requerido
                'monto' => 'required|numeric|min:0', // Monto requerido, mínimo 0
                'descripcion' => 'nullable|string' // Descripción opcional
            ]);

            // Crea una nueva instancia de Wallet
            $wallet = new Wallet();
            $wallet->user_id = $request->user()->id; // Asigna el ID del usuario autenticado
            $wallet->nombre = $request->nombre; // Nombre de la billetera
            $wallet->monto = $request->monto; // Monto inicial
            $wallet->descripcion = $request->descripcion; // Descripción (opcional)
            $wallet->save(); // Guarda la billetera en la base de datos

            // Crea el primer movimiento (apertura de billetera)
            $first_movement = new Movement();
            $first_movement->wallet_id = $wallet->id; // Asigna el ID de la billetera creada
            $first_movement->mes = date('Y-m'); // Mes actual en formato YYYY-MM
            $first_movement->ingreso = $request->monto; // Monto inicial como ingreso
            $first_movement->gasto = 0; // Sin gastos iniciales
            $first_movement->detalle_ingreso = 'Apertura de billetera'; // Detalle del ingreso
            $first_movement->save(); // Guarda el movimiento en la base de datos

            // Retorna la billetera creada
            return response()->json([
                'wallet' => $wallet
            ]);
        } catch (ValidationException $e) {
            // Si hay errores de validación, los retorna
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Elimina una billetera específica del usuario autenticado
     * @param Request $request - Solicitud HTTP con ID de la billetera
     * @return \Illuminate\Http\JsonResponse - Mensaje de confirmación o errores
     */
    public function deleteWallet(Request $request)
    {
        try {
            // Valida que el ID de la billetera exista en la base de datos
            $request->validate([
                'wallet_id' => 'required|exists:billeteras,id'
            ]);

            // Busca la billetera que pertenece al usuario autenticado
            $wallet = Wallet::where('id', $request->wallet_id)
                ->where('user_id', $request->user_id) // Nota: debería ser $request->user()->id
                ->firstOrFail(); // Lanza excepción si no encuentra la billetera

            // Elimina la billetera de la base de datos
            $wallet->delete();

            // Retorna mensaje de confirmación
            return response()->json([
                'message' => 'Cartera eliminada correctamente'
            ]);
        } catch (ValidationException $e) {
            // Si hay errores de validación, los retorna
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Obtiene todos los movimientos de una billetera específica
     * @param Request $request - Solicitud HTTP con ID de la billetera
     * @return \Illuminate\Http\JsonResponse - Lista de movimientos o errores
     */
    public function getMovementsbyWallet(Request $request)
    {
        try {
            // Valida que el ID de la billetera exista
            $request->validate([
                'wallet_id' => 'required|exists:billeteras,id'
            ]);

            // Busca la billetera que pertenece al usuario autenticado
            $wallet = Wallet::where('id', $request->wallet_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail(); // Lanza excepción si no encuentra la billetera

            // Obtiene todos los movimientos de la billetera ordenados por fecha de creación (más recientes primero)
            $movements = Movement::where('wallet_id', $wallet->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Retorna los movimientos en formato JSON
            return response()->json($movements);
        } catch (ValidationException $e) {
            // Si hay errores de validación, los retorna
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Agrega un nuevo movimiento (ingreso o gasto) a una billetera específica
     * @param Request $request - Solicitud HTTP con datos del movimiento
     * @return \Illuminate\Http\JsonResponse - Movimiento creado o errores
     */
    public function addMovementsbyWallet(Request $request)
    {
        try {
            // Valida los datos de entrada para el nuevo movimiento
            $request->validate([
                'wallet_id' => 'required|exists:billeteras,id', // ID de billetera válido
                'tipo' => 'required|in:Ingreso,Gasto', // Tipo debe ser Ingreso o Gasto
                'monto' => 'required|numeric|min:0', // Monto requerido, mínimo 0
                'detalle' => 'required|string', // Detalle requerido
                'mes' => 'required|date_format:Y-m' // Mes en formato YYYY-MM
            ]);

            // Busca la billetera que pertenece al usuario autenticado
            $wallet = Wallet::where('id', $request->wallet_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail(); // Lanza excepción si no encuentra la billetera

            // Crea una nueva instancia de Movement
            $movement = new Movement();
            $movement->wallet_id = $wallet->id; // Asigna el ID de la billetera
            $movement->mes = $request->mes; // Mes del movimiento

            // Procesa el movimiento según el tipo (Ingreso o Gasto)
            if ($request->tipo == 'Ingreso') {
                // Si es un ingreso, llena los campos de ingreso
                $movement->ingreso = $request->monto; // Monto del ingreso
                $movement->detalle_ingreso = $request->detalle; // Detalle del ingreso
                $movement->detalle_gasto = null; // Sin detalle de gasto
                $movement->gasto = null; // Sin gasto
                $wallet->monto += $request->monto; // Aumenta el saldo de la billetera
            } else {
                // Si es un gasto, llena los campos de gasto
                $movement->gasto = $request->monto; // Monto del gasto
                $movement->detalle_gasto = $request->detalle; // Detalle del gasto
                $movement->ingreso = null; // Sin ingreso
                $movement->detalle_ingreso = null; // Sin detalle de ingreso
                $wallet->monto -= $request->monto; // Disminuye el saldo de la billetera
            }

            // Guarda el movimiento en la base de datos
            $movement->save();

            // Actualiza el saldo de la billetera en la base de datos
            $wallet->save();

            // Retorna el movimiento creado
            return response()->json([
                'movement' => $movement
            ]);
        } catch (ValidationException $e) {
            // Si hay errores de validación, los retorna
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
