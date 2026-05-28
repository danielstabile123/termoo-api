<?php

/*
| Banco de dados — neste projeto NÃO usamos tabelas SQL.
| A partida fica em arquivo JSON (storage/app/games/).
|
| Mantemos sqlite em memória só para o Laravel subir sem erro.
*/

return [

    'default' => env('DB_CONNECTION', 'sqlite'),

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', ':memory:'),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

    ],

];
