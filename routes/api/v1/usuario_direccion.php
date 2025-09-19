<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UsuarioDireccionController;

Route::post('/guardar-usuario-direccion', [UsuarioDireccionController::class, 'guardar_usuario_direccion']);