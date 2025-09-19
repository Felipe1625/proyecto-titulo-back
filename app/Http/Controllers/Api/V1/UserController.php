<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    public function actualizar_ciudad_usuario(Request $request)
    {
        // 1. Validar la solicitud.
        // Se asegura de que el ID del usuario sea un número entero y que la ciudad sea una cadena no vacía.
        $request->validate([
            'id_usuario'    => 'required|integer',
            'ciudad_usuario' => 'required|string|max:255',
        ]);

        try {
            // 2. Buscar al usuario por su ID.
            // Si el usuario no existe, `findOrFail` lanzará una excepción.
            $usuario = User::findOrFail($request->input('id_usuario'));

            // 3. Actualizar el campo 'ciudad_usuario'.
            $usuario->ciudad_usuario = $request->input('ciudad_usuario');

            // 4. Guardar los cambios en la base de datos.
            $usuario->save();

            // 5. Retornar una respuesta exitosa.
            return response()->json([
                'message' => 'Ciudad de usuario actualizada exitosamente.',
                'user' => [
                    'id'     => $usuario->id_usuario,
                    'nombre' => $usuario->nombre_usuario,
                    'ciudad' => $usuario->ciudad_usuario,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // 6. Manejo de error si el usuario no es encontrado.
            return response()->json([
                'message' => 'Usuario no encontrado.',
                'error'   => $e->getMessage(),
            ], 404);

        } catch (\Exception $e) {
            // 7. Manejo de cualquier otro error general.
            Log::error('Error al actualizar la ciudad del usuario: ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al intentar actualizar la ciudad del usuario.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function obtener_listado_usuarios()
    {
        try {
            // Utiliza el método `all()` de Eloquent para obtener todos los registros de la tabla 'users'.
            $usuarios = User::all();

            // Retorna una respuesta JSON con la lista de usuarios.
            return response()->json([
                'message' => 'Listado de usuarios obtenido exitosamente.',
                'data'    => $usuarios,
                'count'   => $usuarios->count(),
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre un error, registra el error y retorna una respuesta 500.
            Log::error('Error al obtener el listado de usuarios: ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al obtener el listado de usuarios.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function obtener_eventos_usuario(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'id_usuario' => 'required|integer|exists:usuario,id_usuario',
        // ]);

        $request->validate([
            'id_usuario' => 'required|integer|exists:usuario,id_usuario',
        ]);

        try {
            $id = $request->input('id_usuario');

            $user = User::where('id_usuario', $id)
                        ->with('eventoUsuarioDetalle')             
                        ->with('eventosOrganizadosDetalle')         
                        ->with('eventoUsuarioInvitacionDetalle') 
                        ->withCount('eventoUsuarioDetalle') 
                        ->withCount('eventosOrganizadosDetalle')  
                        ->withCount('eventoUsuarioInvitacionDetalle') 
                        ->firstOrFail();

            return response()->json([
                'message' => 'Estructura de datos de usuario recuperada.',
                'user' => $user->toArray()
            ],200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Usuario no encontrado.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener los eventos del usuario.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}