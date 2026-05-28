<?php

/*
|--------------------------------------------------------------------------
| CORS — Cross-Origin Resource Sharing
|--------------------------------------------------------------------------
|
| O front do professor (termorest.conradosal.com) roda em OUTRO domínio
| que a nossa API (railway.app). Sem CORS, o navegador bloqueia o fetch.
|
| Este arquivo libera apenas o domínio do professor a chamar nossa API.
|
*/

return [

    // Quais URLs da nossa API aceitam requisição cross-origin
    'paths' => ['api/*', 'jogos', 'jogos/*', 'sanctum/csrf-cookie'],

    // GET, POST, etc.
    'allowed_methods' => ['*'],

    // Só este site pode chamar a API a partir do navegador
    'allowed_origins' => [
        'https://termorest.conradosal.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
