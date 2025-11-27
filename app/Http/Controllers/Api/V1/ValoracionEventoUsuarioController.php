<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ValoracionEventoUsuario;
use App\Models\ValoracionEventoUsuarioProblema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class ValoracionEventoUsuarioController extends Controller
{

    public function guardar_valoracion_evento_usuario(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|integer|exists:usuario,id_usuario',
            'id_evento' => 'required|integer|exists:evento,id_evento',
            'calificacion' => 'required|integer|min:1|max:5',
            'problemas_ids' => 'nullable|array',
            'problemas_ids.*' => 'integer|exists:problema_valoracion_evento,id_problema_valoracion_evento',
        ]);

        $data = $validator->validated();
        $problemas_ids = $data['problemas_ids'] ?? [];

        try {
            DB::beginTransaction();

            $valoracion = ValoracionEventoUsuario::create([
                'id_usuario' => $data['id_usuario'],
                'id_evento' => $data['id_evento'],
                'calificacion' => $data['calificacion'],
                'fecha_valoracion' => Carbon::now(),
            ]);

            if (!empty($problemas_ids)) {
                $registrosProblemas = [];
                $fechaActual = Carbon::now();

                foreach ($problemas_ids as $problema_id) {
                    $registrosProblemas[] = [
                        'id_valoracion' => $valoracion->id_valoracion,
                        'id_problema_valoracion_evento' => $problema_id,
                        'id_usuario' => $data['id_usuario'],
                        'id_evento' => $data['id_evento'],
                    ];
                }

                ValoracionEventoUsuarioProblema::insert($registrosProblemas);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Valoración guardada exitosamente.',
                'data' => [
                    'id_valoracion' => $valoracion->id_valoracion,
                    'problemas_asociados' => count($problemas_ids)
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la valoración y sus problemas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}