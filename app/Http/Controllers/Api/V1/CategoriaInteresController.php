<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaInteres;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class CategoriaInteresController extends Controller
{
    public function listado()
    {
        $categorias = CategoriaInteres::all();
        return response()->json($categorias);
    }

    public function listado_categoria_intereses()
    {
        $categorias = CategoriaInteres::with('intereses')->get();
        return response()->json($categorias);
    }
}