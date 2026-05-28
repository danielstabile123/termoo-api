<?php

/*
| Configurações gerais da aplicação (nome, ambiente, idioma, chave).
| Valores sensíveis vêm do .env no servidor (Railway → Variables).
*/

return [

    'name' => env('APP_NAME', 'TermooAPI'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => 'America/Sao_Paulo',

    'locale' => 'pt_BR',

    'fallback_locale' => 'pt_BR',

    'faker_locale' => 'pt_BR',

    // Chave de criptografia do Laravel (obrigatória em produção)
    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
    ],

];
