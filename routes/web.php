<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\ReservaController;

Route::get('/', [ReservaController::class, 'formularioCrear']); // Para mostrar el dashboard en Bootstrap
Route::post('/reservar', [ReservaController::class, 'crear']); // Para procesar el formulario
Route::post('/cancelar/{id}', [ReservaController::class, 'cancelar']); // Para procesar la cancelacion

Route::post('/cancelar/{id}', [ReservaController::class, 'cancelar']);