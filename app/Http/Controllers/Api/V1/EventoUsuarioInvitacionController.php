<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventoUsuarioInvitacion;
use App\Models\Evento;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventoUsuarioInvitacionController extends Controller
{
    public function enviar_invitaciones(Request $request)
    {   
        // Validación de la request
        $validator = Validator::make($request->all(), [
            'evento.id_evento' => 'required|integer|exists:evento,id_evento',
            'usuarios'         => 'required|array|min:1',
            'usuarios.*'       => 'integer|exists:usuario,id_usuario',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $idEvento = $request->input('evento.id_evento');
            $usuarios = $request->input('usuarios');

            // Buscar el evento y obtener organizador
            $evento = Evento::findOrFail($idEvento);
            $idOrganizador = $evento->fk_id_organizador;

            // Excluir al organizador si aparece en el listado
            $usuarios = array_filter($usuarios, function ($usuarioId) use ($idOrganizador) {
                return $usuarioId != $idOrganizador;
            });

            // Recorremos los usuarios e insertamos invitaciones
            foreach ($usuarios as $usuarioId) {
                EventoUsuarioInvitacion::create([
                    'fk_id_evento'       => $idEvento,
                    'fk_id_usuario'      => $usuarioId,
                    'fk_id_organizador'  => $idOrganizador,
                    'estado_invitacion'  => EventoUsuarioInvitacion::ESTADO_ESPERA,
                    'fecha_invitacion'   => Carbon::now(),
                    'mensaje_organizador'=> null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Invitaciones enviadas correctamente',
                'data'  => true,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al enviar invitaciones',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
