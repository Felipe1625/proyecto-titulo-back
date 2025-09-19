<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsuarioDireccion;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class UsuarioDireccionController extends Controller
{
    public function guardar_usuario_direccion(Request $request)
    {   
        $request->validate([
            'id_usuario' => 'required|integer|exists:usuario,id_usuario', // Valida que el usuario exista
            'nombre_direccion' => 'required|string|max:255',
            'latitud_direccion' => 'required|numeric',
            'longitud_direccion' => 'required|numeric',
        ]);
        // 1. Definir las reglas de validación para los datos entrantes.
        // Se asume que el JSON tiene las claves id_usuario, nombre_direccion, latitud_direccion y longitud_direccion.

        try {
            // 3. Crear y guardar la nueva dirección en la base de datos usando el modelo UsuarioDireccion.
            $nuevaDireccion = UsuarioDireccion::create([
                'fk_id_usuario' => $request->id_usuario,
                'nombre_direccion' => $request->nombre_direccion,
                'latitud_direccion' => $request->latitud_direccion,
                'longitud_direccion' => $request->longitud_direccion,
                'direccion_verificada' =>true
            ]);

            // 4. Retornar una respuesta de éxito con los datos de la dirección creada.
            return response()->json([
                'status' => 'success',
                'message' => 'Dirección de usuario guardada exitosamente.',
                'data' => $nuevaDireccion
            ], 201); // Código de estado HTTP 201 para "Creado"

        } catch (\Exception $e) {
            // 5. En caso de error inesperado, retornar un mensaje de error genérico.
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al guardar la dirección.',
                'error_details' => $e->getMessage()
            ], 500); // Código de estado HTTP 500 para errores del servidor
        }
    }
}