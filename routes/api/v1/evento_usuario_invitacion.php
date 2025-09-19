<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EventoUsuarioInvitacionController;

Route::post('/enviar-invitaciones', [EventoUsuarioInvitacionController::class, 'enviar_invitaciones']);