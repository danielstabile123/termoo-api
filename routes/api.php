<?php

use App\Http\Controllers\TermooController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API (prefixo automático: /api)
|--------------------------------------------------------------------------
|
| Estas rotas atendem o ENUNCIADO da disciplina.
| O Laravel adiciona "/api" na frente de cada URL aqui definida.
|
| Exemplos reais:
|   POST /api/iniciar-jogo
|   POST /api/validar-tentativa
|
*/

// Cria partida nova e devolve idJogo + regras (5 letras, 6 tentativas)
Route::post('/iniciar-jogo', [TermooController::class, 'iniciarJogo']);

// Recebe idJogo + palavra e devolve as cores de cada letra
Route::post('/validar-tentativa', [TermooController::class, 'validarTentativa']);
