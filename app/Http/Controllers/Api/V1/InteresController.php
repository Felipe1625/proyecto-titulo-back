<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interes;
use App\Models\UsuarioInteres;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class InteresController extends Controller
{

    public function listado()
    {
        $intereses = Interes::with('categoriaInteres')->get();
        return response()->json($intereses);
    }


    public function interes_por_categoria($id_categoria)
    {
        $intereses = Interes::where('id_categoria_interes', $id_categoria)
                           ->with('categoriaInteres')
                           ->get();
        return response()->json($intereses);
    }

    public function guardar_usuario_interes(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|integer',
            'interes' => 'required|array',
            'interes.*' => 'integer'
        ]);

        $id_usuario = $request->input('id_usuario');
        $nuevos_intereses_ids = $request->input('interes');

        DB::beginTransaction();

        try {
            $intereses_existentes_ids = UsuarioInteres::where('id_usuario', $id_usuario)
                                                      ->pluck('id_interes')
                                                      ->toArray();

            $intereses_a_insertar = array_diff($nuevos_intereses_ids, $intereses_existentes_ids);

            $datos_a_insertar = [];
            foreach ($intereses_a_insertar as $id_interes) {
                $datos_a_insertar[] = [
                    'id_usuario' => $id_usuario,
                    'id_interes' => $id_interes
                ];
            }

            if (!empty($datos_a_insertar)) {
                UsuarioInteres::insert($datos_a_insertar);
            }

            DB::commit();
            return response()->json(['message' => 'intereses de usuario guardados correctamente.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al guardar los intereses.', 'error' => $e->getMessage()], 500);
        }
    }

    public function listar_usuario_interes($id_usuario)
    {
        try {
            $intereses = UsuarioInteres::where('id_usuario', $id_usuario)
                                       ->join('interes', 'usuario_interes.id_interes', '=', 'interes.id_interes')
                                       ->select('interes.*')
                                       ->get();

            return response()->json($intereses);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al listar los intereses.', 'error' => $e->getMessage()], 500);
        }
    }
}