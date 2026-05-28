<?php

use App\Http\Controllers\TermooController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Web (sem prefixo /api)
|--------------------------------------------------------------------------
|
| Usadas para:
| 1) Testar se a API está no ar (GET /)
| 2) Compatibilidade com o front do professor (termorest.conradosal.com)
|
*/

// Health check: abrir a URL base no navegador mostra que a API está online
Route::get('/', function () {
    return response()->json(['status' => 'Termoo API online']);
});

// --- Formato do site do professor (URLs diferentes do enunciado) ---

// Equivalente ao iniciar-jogo: POST /jogos
Route::post('/jogos', [TermooController::class, 'iniciarJogo']);

// Equivalente ao validar-tentativa: POST /jogos/{id}/tentativas
Route::post('/jogos/{idJogo}/tentativas', [TermooController::class, 'validarTentativaProfessor']);
