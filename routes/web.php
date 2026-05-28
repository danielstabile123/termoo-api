<?php

use App\Http\Controllers\TermooController;
use Illuminate\Support\Facades\Route;

// Páginas HTML simples
Route::get('/', function () {
    return redirect('/termo.html');
});

// Rotas usadas pelo front oficial do professor (termorest.conradosal.com)
Route::post('/jogos', [TermooController::class, 'iniciarJogo']);
Route::post('/jogos/{idJogo}/tentativas', [TermooController::class, 'validarTentativaProfessor']);
