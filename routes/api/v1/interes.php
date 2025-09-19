<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\InteresController;

Route::get('/listado', [InteresController::class, 'listado']);
Route::get('/interes-por-categoria/{id_categoria}', [InteresController::class, 'interes_por_categoria']);
Route::post('/guardar-usuario-interes', [InteresController::class, 'guardar_usuario_interes']);
Route::get('/listar-usuario-interes/{id_usuario}', [InteresController::class, 'listar_usuario_interes']);