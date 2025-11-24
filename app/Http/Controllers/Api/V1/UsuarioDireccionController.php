<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsuarioDireccion;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Support\Facades\Storage;

class UsuarioDireccionController extends Controller
{
    public function guardar_usuario_direccion2(Request $request)
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
            if(detectarDireccionBoleta()){
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
            }else{
                return response()->json([
                'status' => 'error',
                'message' => 'No se detecto una coincidencia entre la boleta y la dirección ingresada, verificar documento.',
                'error_details' => $e->getMessage()
                ], 500); // Código de estado HTTP 500 para errores del servidor
            }

        } catch (\Exception $e) {
            // 5. En caso de error inesperado, retornar un mensaje de error genérico.
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al guardar la dirección.',
                'error_details' => $e->getMessage()
            ], 500); // Código de estado HTTP 500 para errores del servidor
        }
    }


    public function guardar_usuario_direccion(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|integer|exists:usuario,id_usuario',
            'nombre_direccion' => 'required|string|max:255',
            'latitud_direccion' => 'required|numeric',
            'longitud_direccion' => 'required|numeric',
            'archivo_verificacion' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            $archivoBoleta = $request->file('archivo_verificacion');
            $carpetaDestino = 'boletas_verificacion/' . $request->id_usuario;
            $rutaArchivoLocal = Storage::putFile($carpetaDestino, $archivoBoleta, 'public');

            $rutaAbsoluta = Storage::path($rutaArchivoLocal,$nombre_direccion);

            if ($this->detectarDireccionBoleta($rutaAbsoluta)) {
                $nuevaDireccion = UsuarioDireccion::create([
                    'fk_id_usuario' => $request->id_usuario,
                    'nombre_direccion' => $request->nombre_direccion,
                    'latitud_direccion' => $request->latitud_direccion,
                    'longitud_direccion' => $request->longitud_direccion,
                    'direccion_verificada' => true,
                    'ruta_boleta' => $rutaArchivoLocal,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Dirección de usuario guardada y verificada exitosamente.',
                    'data' => $nuevaDireccion
                ], 201);
            } else {
                // Si la verificación falla, eliminar el archivo subido
                Storage::delete($rutaArchivoLocal); 

                return response()->json([
                    'status' => 'error',
                    'message' => 'No se detectó una coincidencia entre la boleta y la dirección ingresada, verificar documento.',
                ], 400); 
            }

        } catch (\Exception $e) {
            // 6. Manejo de errores
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al guardar o verificar la dirección.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    public function detectarDireccionBoleta($rutaAbsoluta,$nombre_direccion)
    {
        $rutaImagen = $rutaAbsoluta;

        try {
            $client = new ImageAnnotatorClient();
            $image = file_get_contents($rutaImagen);

            $response = $client->textDetection($image);
            $texts = $response->getTextAnnotations();

            $client->close();

            $resultado = [];
            foreach ($texts as $text) {
                $resultado[] = $text->getDescription();
            }

            // El primer elemento es TODO el texto
            $textoCompleto = $resultado[0] ?? '';

            $direccionExiste = stripos($textoCompleto, $nombre_direccion) !== false;

            return $direccionExiste;

        } catch (\Exception $e) {
            return false;
        }
    }
}