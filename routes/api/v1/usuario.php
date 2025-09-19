<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;

Route::post('/actualizar-ciudad-usuario', [UserController::class, 'actualizar_ciudad_usuario']);
Route::get('/obtener-listado-usuarios', [UserController::class, 'obtener_listado_usuarios']);
Route::post('/obtener-eventos-usuario', [UserController::class, 'obtener_eventos_usuario']);
