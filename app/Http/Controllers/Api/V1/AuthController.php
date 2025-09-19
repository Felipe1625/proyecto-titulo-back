<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse; // Se ha añadido esta importación
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

class AuthController extends Controller
{
    // public function registro(RegisterRequest $request)
    // {
    //     try{
    //         $validatedData = $request->validated();
    //         $usuario = User::create([
    //             'id_tipo_usuario' => 2,
    //             'nombre_usuario' => $validatedData['nombre_usuario'],
    //             'email_usuario' => $validatedData['email_usuario'],
    //             'password_usuario' => $validatedData['password_usuario'],
    //         ]);

    //         //token de Laravel Sanctum.
    //         $token = $usuario->createToken('auth_token')->plainTextToken;
    //         //json sin los datos de usuario
    //         return Response::json([
    //             'message' => 'Usuario registrado exitosamente',
    //             'access_token' => $token,
    //             'token_type' => 'Bearer',
    //             // Se añade el objeto 'user' con los datos del usuario.
    //             'user' => [
    //                 'id' => $usuario->id_usuario,
    //                 'nombre' => $usuario->nombre_usuario,
    //                 'email' => $usuario->email_usuario,
    //             ]
    //         ], 201);

    //     } catch (\Exception $e) {
    //         // Log::error('Error en el registro de usuario: ' . $e->getMessage());
    //         return Response::json([
    //             'message' => 'Ocurrió un error al intentar registrar el usuario.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function registro(RegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // 1. Crear una base para el alias a partir del nombre de usuario.
            // Se convierte a minúsculas y se remueven caracteres que no sean alfanuméricos.
            $baseAlias = Str::slug($validatedData['nombre_usuario'], '');

            $aliasUsuario = '';
            $isAliasUnique = false;

            // 2. Generar un alias único. Se usa un bucle para asegurar que el alias no exista.
            do {
                // Genera un número aleatorio de 4 dígitos.
                $randomNumber = random_int(1000, 9999);
                // Combina la base del alias con el número aleatorio.
                $aliasUsuario = $baseAlias . $randomNumber;

                // Verifica si el alias ya existe en la base de datos.
                $isAliasUnique = !User::where('alias_usuario', $aliasUsuario)->exists();

            } while (!$isAliasUnique);

            // 3. Crear el usuario incluyendo el alias generado.
            $user = User::create([
                'id_tipo_usuario' => 2,
                'nombre_usuario' => $validatedData['nombre_usuario'],
                'email_usuario' => $validatedData['email_usuario'],
                'password_usuario' => $validatedData['password_usuario'],
                'alias_usuario' => $aliasUsuario, // Se añade la nueva columna
            ]);

            // Se genera el token de Laravel Sanctum.
            $token = $user->createToken('auth_token')->plainTextToken;

            // $authController = new AuthController();
            // $userData = $authController->obtenerDatosCompletosUsuario($user->id_usuario)->getData();

            $userData = $this->obtenerDatosCompletosUsuario($user->id_usuario)->getData();

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $userData->user,
            ]);


            // obtenerDatosCompletosUsuario($usuario->id_usuario);
            // 4. Retornar la respuesta JSON con todos los datos.
            // return Response::json([
            //     'message' => 'Usuario registrado exitosamente',
            //     'access_token' => $token,
            //     'token_type' => 'Bearer',
            //     'user' => [
            //         'id' => $usuario->id_usuario,
            //         'nombre' => $usuario->nombre_usuario,
            //         'email' => $usuario->email_usuario,
            //         'alias' => $usuario->alias_usuario, // Se añade el alias a la respuesta
            //     ]
            // ], 201);

        } catch (\Exception $e) {
            Log::error('Error en el registro de usuario: ' . $e->getMessage());
            return Response::json([
                'message' => 'Ocurrió un error al intentar registrar el usuario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt([
            'email_usuario' => $request->email_usuario,
            'password' => $request->password_usuario
        ])) {
            return response()->json([
                'message' => 'Credenciales inválidas.'
            ], 401);
        }

        $user = User::where('email_usuario', $request->email_usuario)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        \Log::info('Objeto de usuario recuperado en login:', $user->toArray());

        $userDataResponse = $this->obtenerDatosCompletosUsuario($user->id_usuario);
        $userData = $userDataResponse->getData(true);

        $response = array_merge(
            [
                'message' => 'Inicio de sesión exitoso',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            $userData
        );

        return response()->json($response);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.'
        ]);
    }

    public function obtenerDatosCompletosUsuario(int $id): JsonResponse
    {
        try {

            $user = User::where('id_usuario', $id)
                        ->with('intereses')
                        ->with('eventoUsuarioDetalle') 
                        ->with('eventoUsuarioInvitacionDetalle') 
                        ->with('usuarioDireccion') 
                        ->with('eventosOrganizadosDetalle') 
                        ->withCount('intereses')
                        ->withCount('eventoUsuarioDetalle') 
                        ->withCount('eventoUsuarioInvitacionDetalle') 
                        ->withCount('usuarioDireccion') 
                        ->withCount('eventosOrganizadosDetalle')
                        ->firstOrFail();

            return response()->json([
                'message' => 'Estructura de datos de usuario recuperada.',
                'user' => $user->toArray()
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Usuario no encontrado.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener los datos del usuario.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
