<?php

use App\Http\Controllers\TermooController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/iniciar-jogo', [TermooController::class, 'iniciarJogo']);
Route::post('/validar-tentativa', [TermooController::class, 'validarTentativa']);
