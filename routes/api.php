<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Versión 1 de la API ---
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(base_path('routes/api/v1/auth.php'));
    Route::prefix('categoria-interes')->group(base_path('routes/api/v1/categoria_interes.php'));
    Route::prefix('evento')->group(base_path('routes/api/v1/evento.php'));
    Route::prefix('evento-usuario-invitacion')->group(base_path('routes/api/v1/evento_usuario_invitacion.php'));
    Route::prefix('interes')->group(base_path('routes/api/v1/interes.php'));
    Route::prefix('lugar-publico')->group(base_path('routes/api/v1/lugar_publico.php'));
    Route::prefix('reporte-evento')->group(base_path('routes/api/v1/reporte_evento.php'));
    Route::prefix('reporte-usuario')->group(base_path('routes/api/v1/reporte_usuario.php'));
    Route::prefix('tipo-reporte-evento')->group(base_path('routes/api/v1/tipo_reporte_evento.php'));
    Route::prefix('tipo-reporte-usuario')->group(base_path('routes/api/v1/tipo_reporte_usuario.php'));
    Route::prefix('tipo-usuario')->group(base_path('routes/api/v1/tipo_usuario.php'));
    Route::prefix('usuario')->group(base_path('routes/api/v1/usuario.php'));
    Route::prefix('usuario-direccion')->group(base_path('routes/api/v1/usuario_direccion.php'));
    Route::prefix('usuario-interes')->group(base_path('routes/api/v1/usuario_interes.php'));
});