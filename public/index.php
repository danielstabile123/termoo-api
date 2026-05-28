<?php

/**
 * Ponto de entrada de TODA requisição HTTP no Laravel.
 *
 * Fluxo: navegador → index.php → bootstrap/app.php → rota → controller → JSON
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Modo manutenção (se existir arquivo de manutenção)
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload do Composer (carrega vendor/ com Laravel e dependências)
require __DIR__.'/../vendor/autoload.php';

// Inicia o Laravel e processa a requisição atual
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
