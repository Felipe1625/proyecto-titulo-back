<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas por autenticación (requieren un token válido).
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});