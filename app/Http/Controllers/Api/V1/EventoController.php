<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use App\Models\User;
use App\Models\LugarPublico;
use App\Models\UsuarioDireccion;
use App\Models\Evento;
use App\Models\EventoUsuario; 
use App\Models\EventoInteres; 
use App\Models\EventoUsuarioInvitacion; 
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EventoController extends Controller
{
    /**
     * Crea un nuevo evento público y su ubicación.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function crear_evento_publico(Request $request): JsonResponse
    {
        // 1. Validar los datos de entrada
        $request->validate([
            'lugar.nombre' => 'required|string|max:255',
            'lugar.latitud' => 'required|numeric',
            'lugar.longitud' => 'required|numeric',
            'evento.titulo' => 'required|string|max:255',
            'evento.descripcion' => 'required|string',
            'evento.fecha' => 'required',
            'evento.hora_inicio' => 'required|date_format:g:i A',
            'evento.hora_termino' => 'required|date_format:g:i A',
            'evento.cantidad_personas' => 'required|integer|min:1',
            'evento.id_organizador' => 'required|integer|exists:usuario,id_usuario',
        ]);

        try {
            // 2. Verificar si el id_organizador existe
            $organizador = User::findOrFail($request->input('evento.id_organizador'));
            Log::info("Organizador con ID {$organizador->id_usuario} encontrado.");

            // 3. Obtener los datos del lugar y del evento
            $lugarData = $request->input('lugar');
            $eventoData = $request->input('evento');

            // 4. Crear el registro en la tabla 'lugar_publico'
            $lugarPublico = LugarPublico::create([
                'nombre_lugar' => $lugarData['nombre'],
                'latitud_lugar' => $lugarData['latitud'],
                'longitud_lugar' => $lugarData['longitud'],
            ]);
            Log::info("Lugar público creado con ID: {$lugarPublico->id_lugar}");

            // 5. Formatear la fecha y las horas usando Carbon para la base de datos
            $fechaEvento = Carbon::createFromFormat('d/m/Y', $eventoData['fecha']);
            $horaInicio = Carbon::createFromFormat('g:i A', $eventoData['hora_inicio'])->format('H:i:s');
            $horaTermino = Carbon::createFromFormat('g:i A', $eventoData['hora_termino'])->format('H:i:s');

            // 6. Crear el registro en la tabla 'eventos'
            $evento = Evento::create([
                'nombre_evento' => $eventoData['titulo'],
                'descripcion_evento' => $eventoData['descripcion'],
                'fecha_evento' => $fechaEvento,
                'hora_inicio_evento' => $horaInicio,
                'hora_termino_evento' => $horaTermino,
                'cantidad_personas_evento' => $eventoData['cantidad_personas'],
                'fk_id_organizador' => $organizador->id_usuario,
                'fk_id_lugar_publico' => $lugarPublico->id_lugar, // Se asocia el nuevo lugar
                'estado_evento'=>'activo'
            ]);
            Log::info("Evento público creado con ID: {$evento->id_evento}");

            // 7. Retornar una respuesta JSON exitosa
            return response()->json([
                'message' => 'Evento público creado exitosamente.',
                'data' => $evento->load('lugarPublico'), // Opcional: Cargar la relación para la respuesta
            ], 201);

        } catch (ModelNotFoundException $e) {
            // Manejo de error si el usuario organizador no existe
            Log::error('Error al crear evento: ID de organizador no encontrado.');
            return response()->json([
                'message' => 'El organizador especificado no fue encontrado.',
            ], 404);

        } catch (\Exception $e) {
            // Manejo de cualquier otro error inesperado
            Log::error('Error al crear evento: ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear el evento público.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function crear_evento_particular(Request $request): JsonResponse
    {
        // 1. Validar los datos de entrada
        $request->validate([
            'id_usuario_direccion' => 'required|integer|exists:usuario_direccion,id_usuario_direccion',
            'evento.titulo' => 'required|string|max:255',
            'evento.descripcion' => 'required|string',
            'evento.fecha' => 'required',
            'evento.hora_inicio' => 'required|date_format:g:i A',
            'evento.hora_termino' => 'required|date_format:g:i A',
            'evento.cantidad_personas' => 'required|integer|min:1',
            'evento.id_organizador' => 'required|integer|exists:usuario,id_usuario',
        ]);

        try {
            // 2. Verificar si la dirección del usuario existe y si el organizador existe
            $direccion = UsuarioDireccion::findOrFail($request->input('id_usuario_direccion'));
            Log::info("Dirección de usuario con ID {$direccion->id_usuario_direccion} encontrada.");
            
            $organizador = User::findOrFail($request->input('evento.id_organizador'));
            Log::info("Organizador con ID {$organizador->id_usuario} encontrado.");

            // 3. Obtener los datos del evento
            $eventoData = $request->input('evento');

            // 4. Formatear la fecha y las horas usando Carbon para la base de datos
            $fechaEvento = Carbon::createFromFormat('d/m/Y', $eventoData['fecha']);
            $horaInicio = Carbon::createFromFormat('g:i A', $eventoData['hora_inicio'])->format('H:i:s');
            $horaTermino = Carbon::createFromFormat('g:i A', $eventoData['hora_termino'])->format('H:i:s');

            // 5. Crear el registro en la tabla 'eventos'
            // NOTA: Se asume que la tabla 'eventos' tiene una columna `fk_id_usuario_direccion`
            // para referenciar las direcciones de los usuarios. También se añade 'tipo_evento'.
            $evento = Evento::create([
                'nombre_evento' => $eventoData['titulo'],
                'descripcion_evento' => $eventoData['descripcion'],
                'fecha_evento' => $fechaEvento,
                'hora_inicio_evento' => $horaInicio,
                'hora_termino_evento' => $horaTermino,
                'cantidad_personas_evento' => $eventoData['cantidad_personas'],
                'fk_id_organizador' => $organizador->id_usuario,
                'fk_id_direccion_particular' => $direccion->id_usuario_direccion, // Se asocia la dirección del usuario
                'fk_id_lugar_publico' => null
            ]);
            Log::info("Evento particular creado con ID: {$evento->id_evento}");

            // 6. Retornar una respuesta JSON exitosa
            return response()->json([
                'message' => 'Evento particular creado exitosamente.',
                'data' => $evento->load('direccionParticular'), // Cargar la relación para la respuesta
            ], 201);

        } catch (ModelNotFoundException $e) {
            // Manejo de error si la dirección o el organizador no existen
            Log::error('Error al crear evento particular: Dirección o ID de organizador no encontrados.');
            return response()->json([
                'message' => 'La dirección o el organizador especificado no fueron encontrados.',
            ], 404);

        } catch (\Exception $e) {
            // Manejo de cualquier otro error inesperado
            Log::error('Error al crear evento particular: ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al intentar crear el evento particular.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function obtener_eventos_publicos(): JsonResponse
    {
        try {
            $eventos = Evento::whereNotNull('fk_id_lugar_publico')
                ->with('lugarPublico')
                ->with('intereses')
                ->get();

            return response()->json([
                'message' => 'Eventos públicos recuperados exitosamente.',
                'total'   => $eventos->count(),
                'data'    => $eventos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar obtener los eventos públicos.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function filtrar_eventos_publicos(Request $request): JsonResponse
    {
        try {
            // $filtros = $request->input('filtros', []);
            $filtros = $request->all();

            $query = Evento::query()
            ->whereNotNull('fk_id_lugar_publico')
            ->with('lugarPublico')
            ->with('intereses');

            $query->when(!empty($filtros['fecha']), function ($q) use ($filtros) {
                $tipoFecha = $filtros['fecha'];
                $hoy = Carbon::now('America/Santiago')->startOfDay();
                $finHoy = Carbon::now('America/Santiago')->endOfDay();

                if ($tipoFecha === 'dia') {
                    $q->whereBetween('fecha_evento', [$hoy->toDateString(), $finHoy->toDateString()]);
                } elseif ($tipoFecha === 'semana') {
                    $q->whereBetween(
                        'fecha_evento',
                        [
                            Carbon::now('America/Santiago')->startOfWeek()->toDateString(),
                            Carbon::now('America/Santiago')->endOfWeek()->toDateString()
                        ]
                    );
                } elseif ($tipoFecha === 'mes') {
                    $q->whereMonth('fecha_evento', Carbon::now('America/Santiago')->month);
                }
            });

            if (!empty($filtros['ubicacion']['lat']) && !empty($filtros['ubicacion']['lng'])) {
                $userLat = $filtros['ubicacion']['lat'];
                $userLng = $filtros['ubicacion']['lng'];

                $query->join('lugar_publico', 'evento.fk_id_lugar_publico', '=', 'lugar_publico.id_lugar');

                $distanceFormula = "(6371000 * acos(
                    cos(radians(?)) 
                    * cos(radians(lugar_publico.latitud_lugar)) 
                    * cos(radians(lugar_publico.longitud_lugar) - radians(?)) 
                    + sin(radians(?)) 
                    * sin(radians(lugar_publico.latitud_lugar))
                ))";

                $distanciaMax = (float)$filtros['distancia'];

                if (!empty($distanciaMax) && $distanciaMax !== 0) {
                    $distanceInMeters = $distanciaMax * 1000;
                    $query->whereRaw("$distanceFormula <= ?", [$userLat, $userLng, $userLat, $distanceInMeters]);
                }

                $query->select('evento.*')->selectRaw("$distanceFormula AS distancia", [$userLat, $userLng, $userLat]);
            }


            $eventos = $query->get();

            return response()->json([
                'message' => 'Eventos públicos filtrados exitosamente.',
                'total' => $eventos->count(),
                'data' => $eventos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al filtrar los eventos públicos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtener_detalle_evento(Request $request)
    {
        // 1. Validar que el 'id_evento' se ha proporcionado.
        $request->validate([
            'id_evento' => 'required|integer|exists:evento,id_evento',
        ]);

        $evento = Evento::with([
            'organizador', 
            'intereses',
            'lugarPublico',
            'direccionParticular',
            'eventoUsuarios.usuario', 
            'invitaciones.invitado' 
        ])->find($request->input('id_evento'));

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado.'], 404);
        }

        // 2. Determinar si el evento es público o particular.
        $esPublico = $evento->fk_id_lugar_publico !== null;

        // 3. Contar los cupos y filtrar invitaciones.
        $cuposTomados = $evento->eventoUsuarios->count();

        $invitacionesEnEspera = 0;
        $invitacionesPendientes = collect([]); // Initialize as a collection

        if (!$esPublico) {
            $invitacionesPendientes = $evento->invitaciones
                                            ->where('estado_invitacion', 'en espera')
                                            ->values();
            $invitacionesEnEspera = $invitacionesPendientes->count();
        }

        // 4. Calcular cupos disponibles y si está lleno.
        $capacidad = $evento->cantidad_personas_evento;
        $cuposDisponibles = max(0, $capacidad - ($cuposTomados + $invitacionesEnEspera));
        $estaLleno = ($cuposDisponibles == 0);

        // 5. Determinar la ubicación.
        $ubicacion = $evento->lugarPublico ?? $evento->direccionParticular;
        $ubicacionData = null;
        if ($ubicacion) {
            if ($esPublico) {
                $ubicacionData = [
                    'id' => $ubicacion->id_lugar,
                    'nombre' => $ubicacion->nombre_lugar,
                    'latitud' => $ubicacion->latitud_lugar,
                    'longitud' => $ubicacion->longitud_lugar,
                    'tipo' => 'publico',
                ];
            } else {
                $ubicacionData = [
                    'id' => $ubicacion->id_usuario_direccion,
                    'nombre' => $ubicacion->nombre_direccion,
                    'latitud' => $ubicacion->latitud_direccion,
                    'longitud' => $ubicacion->longitud_direccion,
                    'tipo' => 'particular',
                ];
            }
        }
        
        // 6. Formatear las listas de usuarios e invitados.
        // Use `collect()` to ensure the variable is a Collection
        $asistentesCollection = collect($evento->eventoUsuarios);
        $usuariosAsistentes = $asistentesCollection->map(function ($registro) {
            return [
                'id_usuario' => $registro->usuario->id_usuario,
                'nombre_usuario' => $registro->usuario->nombre_usuario,
            ];
        });

        $invitacionesPendientesCollection = collect($invitacionesPendientes);
        $invitadosPendientes = $invitacionesPendientesCollection->map(function ($invitacion) {
            return [
                'id_invitacion' => $invitacion->id_evento_usuario_invitacion,
                'id_usuario_invitado' => $invitacion->invitado->id_usuario,
                'nombre_usuario_invitado' => $invitacion->invitado->nombre_usuario,
                'mensaje' => $invitacion->mensaje_organizador,
                'fecha_invitacion' => $invitacion->fecha_invitacion,
            ];
        });

        // Use `collect()` for the interests relationship too
        $interesesCollection = collect($evento->intereses);
        $interesesFormateados = $interesesCollection->map(function ($interes) {
            return [
                'id' => $interes->id_interes,
                'nombre' => $interes->nombre_interes,
            ];
        });

        return response()->json([
            'message' => 'Detalle de evento obtenido con exito.',
            'data' => [
                'id_evento' => $evento->id_evento,
                'nombre_evento' => $evento->nombre_evento,
                'descripcion' => $evento->descripcion,
                'fecha_evento' => $evento->fecha_evento,
                'hora_inicio_evento' => $evento->hora_inicio_evento,
                'capacidad_personas_evento' => $capacidad,
                'organizador' => [
                    'id' => $evento->organizador->id_usuario,
                    'nombre' => $evento->organizador->nombre_usuario,
                ],
                'ubicacion' => $ubicacionData,
                'intereses' => $interesesFormateados,
                'cupos' => [
                    'cupos_tomados' => $cuposTomados,
                    'invitaciones_en_espera' => $invitacionesEnEspera,
                    'cupos_disponibles' => $cuposDisponibles,
                    'esta_lleno' => $estaLleno,
                ],
                'asistentes' => $usuariosAsistentes,
                'invitaciones_pendientes' => $invitadosPendientes,
            ] // <-- La coma después de 'invitaciones_pendientes' ha sido eliminada.
        ], 200);

        // // 7. Formatear la respuesta JSON final.
        // return response()->json([
        //     'id_evento' => $evento->id_evento,
        //     'nombre_evento' => $evento->nombre_evento,
        //     'descripcion' => $evento->descripcion,
        //     'fecha_evento' => $evento->fecha_evento,
        //     'hora_inicio_evento' => $evento->hora_inicio_evento,
        //     'capacidad_personas_evento' => $capacidad,
        //     'organizador' => [
        //         'id' => $evento->organizador->id_usuario,
        //         'nombre' => $evento->organizador->nombre_usuario,
        //     ],
        //     'ubicacion' => $ubicacionData,
        //     'intereses' => $interesesFormateados,
        //     'cupos' => [
        //         'cupos_tomados' => $cuposTomados,
        //         'invitaciones_en_espera' => $invitacionesEnEspera,
        //         'cupos_disponibles' => $cuposDisponibles,
        //         'esta_lleno' => $estaLleno,
        //     ],
        //     'asistentes' => $usuariosAsistentes,
        //     'invitaciones_pendientes' => $invitadosPendientes,
        // ], 200);
    }

    public function unirse_evento_publico(Request $request)
    {
        try {
            // 1. Validar la entrada del JSON
            $request->validate([
                'id_evento' => 'required|integer',
                'id_usuario' => 'required|integer',
            ]);

            $idEvento = $request->input('id_evento');
            $idUsuario = $request->input('id_usuario');

            // 2. Verificar la existencia del evento y del usuario
            $evento = Evento::find($idEvento);
            $usuario = User::find($idUsuario);

            if (!$evento || !$usuario) {
                return response()->json(['message' => 'Evento o usuario no encontrado.'], 404);
            }

            // 3. Verificar que el evento es público usando las claves foráneas
            if ($evento->fk_id_lugar_publico === null) {
                 return response()->json(['message' => 'No puedes unirte a este evento. Es privado.'], 403);
            }

            // 4. Verificar si el usuario ya es el organizador
            if ($evento->fk_id_organizador === $idUsuario) {
                return response()->json(['message' => 'El organizador no puede unirse a su propio evento.'], 409);
            }

            // 5. Verificar si el usuario ya está asociado al evento
            $existeInscripcion = EventoUsuario::where('fk_id_evento', $idEvento)
                                              ->where('fk_id_usuario', $idUsuario)
                                              ->exists();

            if ($existeInscripcion) {
                return response()->json(['message' => 'El usuario ya se ha unido a este evento.'], 409);
            }

            // 6. Si todas las validaciones pasan, insertar el registro
            DB::beginTransaction();
            
            $eventoUsuario = new EventoUsuario();
            $eventoUsuario->fk_id_evento = $idEvento;
            $eventoUsuario->fk_id_usuario = $idUsuario;
            $eventoUsuario->save();

            DB::commit();

            return response()->json([
                'data' => true,
                'message' => 'Te has unido al evento con éxito.'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejo de errores de validación
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            // Manejo de otros errores
            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud.', 'error' => $e->getMessage()], 500);
        }
    }

    public function eliminar_evento(Request $request): JsonResponse
    {
        try {
            // 1. Validar que el 'id_evento' está presente en la solicitud.
            $request->validate([
                'id_evento' => 'required|integer|exists:evento,id_evento',
            ]);

            $idEvento = $request->input('id_evento');

            // 2. Iniciar una transacción de base de datos.
            DB::beginTransaction();

            // 3. Eliminar registros relacionados (intereses, invitados, asistentes).
            // No es necesario verificar si existen, el método `delete` simplemente no hará nada si no los encuentra.
            EventoUsuarioInvitacion::where('fk_id_evento', $idEvento)->delete();
            EventoUsuario::where('fk_id_evento', $idEvento)->delete();
            EventoInteres::where('id_evento', $idEvento)->delete();

            // 4. Buscar y eliminar el evento principal.
            $evento = Evento::find($idEvento);
            
            if (!$evento) {
                // Si el evento ya fue borrado por otra petición, se revierte y se retorna un mensaje.
                DB::rollBack();
                return response()->json(['message' => 'El evento no fue encontrado.'], 404);
            }

            $evento->delete();

            // 5. Confirmar la transacción si todo fue exitoso.
            DB::commit();

            return response()->json([
                'data' => true,
                'message' => 'El evento y sus registros relacionados fueron eliminados exitosamente.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'El evento no fue encontrado.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ocurrió un error al eliminar el evento.', 'error' => $e->getMessage()], 500);
        }
    }

    public function responder_evento_usuario_invitacion(Request $request): JsonResponse
    {
        try {
            // 1. Validar la entrada de la solicitud.
            $request->validate([
                'id_evento' => 'required|integer|exists:evento,id_evento',
                'id_usuario' => 'required|integer|exists:usuario,id_usuario',
                'respuesta' => [
                    'required',
                    'string',
                    Rule::in(['aceptada', 'declinada']),
                ],
            ]);

            $idEvento = $request->input('id_evento');
            $idUsuario = $request->input('id_usuario');
            $respuesta = $request->input('respuesta');

            // 2. Iniciar una transacción de base de datos.
            DB::beginTransaction();

            // 3. Buscar y actualizar el registro de la invitación.
            $invitacion = EventoUsuarioInvitacion::where('fk_id_evento', $idEvento)
                ->where('fk_id_usuario', $idUsuario)
                ->first();

            if (!$invitacion) {
                DB::rollBack();
                return response()->json(['message' => 'No se encontró una invitación pendiente para este evento y usuario.'], 404);
            }

            // Actualizar el estado de la invitación.
            $invitacion->estado_invitacion = $respuesta;
            $invitacion->save();

            // 4. Si la respuesta es 'aceptada', insertar un registro en 'evento_usuario'.
            if ($respuesta === 'aceptada') {
                $existeAsistencia = EventoUsuario::where('fk_id_evento', $idEvento)
                    ->where('fk_id_usuario', $idUsuario)
                    ->exists();

                if (!$existeAsistencia) {
                    $asistencia = new EventoUsuario();
                    $asistencia->fk_id_evento = $idEvento;
                    $asistencia->fk_id_usuario = $idUsuario;
                    $asistencia->save();
                }
            }

            // 5. Confirmar la transacción.
            DB::commit();

            return response()->json([
                'data' => true,
                'message' => 'Respuesta a la invitación procesada con éxito.'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejo de errores de validación
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            // Manejo de otros errores
            return response()->json(['message' => 'Ocurrió un error al procesar la respuesta a la invitación.', 'error' => $e->getMessage()], 500);
        }
    }

    public function cambiar_estado_evento(Request $request)
    {
        try {
            $request->validate([
                'id_evento' => 'required|integer',
                'estado_evento' => 'required|string|in:iniciado,finalizado',
            ]);

            $evento = Evento::findOrFail($request->id_evento);

            $evento->estado_evento = $request->estado_evento;
            $evento->save();

            return response()->json(['data' => true], 200);

        } catch (ValidationException $e) {
            return response()->json(['data' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['data' => false, 'error' => 'Ocurrió un error al cambiar el estado del evento.'], 500);
        }
    }

    public function obtener_estado_evento(Request $request)
    {
        try {
            // Validar que el id_evento es un campo requerido y es un entero.
            $request->validate([
                'id_evento' => 'required|integer',
            ]);

            // Buscar el evento por su ID.
            $evento = Evento::findOrFail($request->id_evento);

            // Devolver solo el estado del evento.
            return response()->json([
                'data' => $evento->estado_evento
            ], 200);

        } catch (\Exception $e) {
            // Manejar errores si el evento no se encuentra o hay otro problema.
            return response()->json([
                'error' => 'No se pudo obtener el estado del evento.'
            ], 500);
        }
    }


}