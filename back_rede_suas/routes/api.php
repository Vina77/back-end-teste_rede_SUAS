<?php

/*
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/acessar', [AuthController::class, 'acessar']);
Route::post('/registrar', [AuthController::class, 'registrar']);
Route::get('/listagem-usuarios', [AuthController::class, 'listagemUsuarios']);
