<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EventoController;
use App\Http\Controllers\Api\V1\ValoracionEventoUsuarioController;

Route::post('/crear-evento-publico', [EventoController::class, 'crear_evento_publico']);
Route::post('/crear-evento-particular', [EventoController::class, 'crear_evento_particular']);
Route::get('/obtener-eventos-publicos', [EventoController::class, 'obtener_eventos_publicos']);
Route::post('/filtrar-eventos-publicos', [EventoController::class, 'filtrar_eventos_publicos']);
Route::post('/obtener-detalle-evento', [EventoController::class, 'obtener_detalle_evento']);
Route::post('/unirse-evento-publico', [EventoController::class, 'unirse_evento_publico']);
Route::post('/eliminar-evento', [EventoController::class, 'eliminar_evento']);
Route::post('/responder-evento-usuario-invitacion', [EventoController::class, 'responder_evento_usuario_invitacion']);
Route::post('/cambiar-estado-evento', [EventoController::class, 'cambiar_estado_evento']);
Route::post('/obtener-estado-evento', [EventoController::class, 'obtener_estado_evento']);
Route::get('/listado-problema-valoracion-evento', [EventoController::class, 'listado_problema_valoracion_evento']);
Route::post('/guardar-valoracion-evento-usuario', [ValoracionEventoUsuarioController::class, 'guardar_valoracion_evento_usuario']);