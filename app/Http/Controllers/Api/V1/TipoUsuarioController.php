<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TipoUsuario; // Importamos el modelo de TipoUsuario.
use Illuminate\Http\Request;

class TipoUsuarioController extends Controller
{
    public function index()
    {
        return response()->json(TipoUsuario::all());
    }

    public function show(string $id_tipo_usuario)
    {
        $tipoUsuario = TipoUsuario::find($id_tipo_usuario);
        if (!$tipoUsuario) {
            return response()->json(['message' => 'Tipo de usuario no encontrado'], 404);
        }
        return response()->json($tipoUsuario);
    }
}
