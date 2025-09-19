<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoriaInteresController;

Route::get('/listado', [CategoriaInteresController::class, 'listado']);
Route::get('/listado-categoria-intereses', [CategoriaInteresController::class, 'listado_categoria_intereses']);