<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TipoUsuarioController;

Route::get('/lista', [TipoUsuarioController::class, 'index']);
Route::get('/buscar/{id}', [TipoUsuarioController::class, 'show']);