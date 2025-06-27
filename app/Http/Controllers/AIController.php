<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Controlador para manejar las interacciones con la IA (CatIA)
 * Gestiona las consultas a la API de NVIDIA para obtener respuestas de una IA experta en finanzas
 */
class AIController extends Controller
{

    /**
     * Método principal para obtener una opinión financiera personalizada de CatIA
     * @param Request $request - Solicitud HTTP que contiene los datos del usuario
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con la opinión de CatIA
     */
    public function catia(Request $request)
    {
        // Obtiene la clave API de NVIDIA desde la configuración de servicios
        $key = config('services.nvidia.api_key');

        // Convierte los datos del usuario a formato JSON para enviarlos a la IA
        $datos = json_encode($request->datos_del_usuario);

        // Define el contexto/prompt que se enviará a la IA
        // Incluye instrucciones específicas sobre el personaje, formato y restricciones
        $contexto ="Eres una gata llamada CatIA y eres una experta en finanazas personales y con muy buen humor.
        \nY con base a estos datos: " . $datos . ", debes darme una opinion de como va mi vida financiera en menos de 70 palabras.
        \nY no me des una respuesta general, sino que me des una respuesta personalizada.
        \nRecuerda que es en pesos colombianos.
        \nImportante no debes sugerir continuar la conversacion.
        \nY no referirte a ti en tercera persona.";

        // Estructura el payload que se enviará a la API de NVIDIA
        $payload = [
            'model' => 'meta/llama-3.1-70b-instruct', // Especifica el modelo de IA a usar
            'messages' => [
                [
                    'role' => 'user', // Define el rol como usuario (quien hace la pregunta)
                    'content' => $contexto // Contenido del mensaje con las instrucciones
                ]
            ],
            'temperature' => 1, // Controla la creatividad de las respuestas (0-1, donde 1 es más creativo)
            'top_p' => 0.7, // Controla la diversidad de las respuestas
            'max_tokens' => 2048, // Número máximo de tokens en la respuesta
            'stream' => false // Desactiva el streaming de respuestas
        ];

        try {
            // Realiza la petición HTTP POST a la API de NVIDIA
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $key, // Incluye la clave API en el header de autorización
                'Content-Type' => 'application/json', // Especifica que se envía JSON
            ])->post('https://integrate.api.nvidia.com/v1/chat/completions', $payload);
        } catch (\Exception $e) {
            // Si hay un error en la petición, retorna un mensaje de error
            return response()->json(['error' => 'Error al obtener la respuesta de la API']);
        }

        // Extrae el contenido de la respuesta de la IA y lo retorna en formato JSON
        // La respuesta viene anidada en choices[0].message.content
        return response()->json(['response' => $response->json()['choices'][0]['message']['content']]);
    }

    /**
     * Método para obtener consejos financieros personalizados de CatIA
     * @param Request $request - Solicitud HTTP que contiene nombre y ciudad del usuario
     * @return \Illuminate\Http\JsonResponse - Respuesta JSON con el consejo de CatIA
     */
    public function catiaConsejo(Request $request)
    {
        // Obtiene la clave API de NVIDIA desde la configuración de servicios
        $key = config('services.nvidia.api_key');

        // Define el contexto específico para solicitar un consejo financiero
        // Incluye el nombre del usuario y su ciudad para personalizar la respuesta
        $contexto ="Eres CatIA (una gata) y eres una experta en finanazas personales y con muy buen humor.
        \nDebes darme un consejo diferente en menos de 70 palabras, mi nombre es " . $request->nombre_usuario . " vivo en " . $request->ciudad . " y ya me conoces.
        \nY no me des una respuesta general, sino que me des una respuesta personalizada.
        \nRecuerda que es en pesos colombianos.
        \nImportante no debes sugerir continuar la conversacion.
        \nY no referirte a ti en tercera persona.";

        // Estructura el payload para la API de NVIDIA (similar al método anterior)
        $payload = [
            'model' => 'meta/llama-3.1-70b-instruct', // Usa el mismo modelo de IA
            'messages' => [
                [
                    'role' => 'user', // Rol del usuario que solicita el consejo
                    'content' => $contexto // Instrucciones específicas para el consejo
                ]
            ],
            'temperature' => 1, // Mantiene alta creatividad para consejos variados
            'top_p' => 0.7, // Controla la diversidad de las respuestas
            'max_tokens' => 2048, // Límite de tokens para la respuesta
            'stream' => false // Sin streaming
        ];

        try {
            // Realiza la petición HTTP POST a la API de NVIDIA
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $key, // Autenticación con la clave API
                'Content-Type' => 'application/json', // Tipo de contenido JSON
            ])->post('https://integrate.api.nvidia.com/v1/chat/completions', $payload);
        } catch (\Exception $e) {
            // Manejo de errores en caso de fallo en la petición
            return response()->json(['error' => 'Error al obtener la respuesta de la API']);
        }

        // Extrae y retorna el consejo de la IA en formato JSON
        return response()->json(['response' => $response->json()['choices'][0]['message']['content']]);
    }

    public function catiaInvoice(Request $request)
    {

    }

}
