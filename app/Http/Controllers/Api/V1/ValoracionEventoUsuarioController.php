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
use Illuminate\Support\Facades\DB;

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

        $problemas_ids = $data['problemas_ids'] ?? [];
        $problemas_ids = $request->input('problemas_ids') ?? [];

        try {
            DB::beginTransaction();

            $valoracion = ValoracionEventoUsuario::create([
                'id_usuario' => $request->input('id_usuario'),
                'id_evento' => $request->input('id_evento'),
                'calificacion' => $request->input('calificacion'),
                'fecha_valoracion' => Carbon::now(),
            ]);

            if (!empty($problemas_ids)) {
                $registrosProblemas = [];
                $fechaActual = Carbon::now();

                foreach ($problemas_ids as $problema_id) {
                    $registrosProblemas[] = [
                        'id_valoracion' => $valoracion->id_valoracion,
                        'id_problema_valoracion_evento' => $problema_id
                    ];
                }

                ValoracionEventoUsuarioProblema::insert($registrosProblemas);
            }

            DB::commit();
            return response()->json([
                'data' => [
                    'error'=>false,
                    'mensaje' => 'Valoracion guardada de forma correcta.'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'data' => [
                    'error'=>true,
                    'mensaje' => 'Error al guardar la valoración y sus problemas.',
                    'log_error' => $e->getMessage()
                ]
            ], 500);
        }
    }

}