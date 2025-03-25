<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/registrar', [AuthController::class, 'registrar']);
Route::post('/acessar', [AuthController::class, 'acessar']);
Route::get('/listagem-usuarios', [AuthController::class, 'listagemUsuarios']);
