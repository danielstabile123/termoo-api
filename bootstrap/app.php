<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Bootstrap da aplicação Laravel
|--------------------------------------------------------------------------
|
| Este arquivo "liga" rotas, middlewares e tratamento de erros.
| É um dos primeiros arquivos carregados quando chega uma requisição HTTP.
|
*/

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',   // rotas sem /api (/, /jogos, ...)
        api: __DIR__ . '/../routes/api.php',   // rotas com prefixo /api
        apiPrefix: 'api',
        health: '/up', // rota interna de saúde do Laravel
    )
    ->withMiddleware(function (Middleware $middleware) {
        // API REST não usa sessão nem CSRF (evita erro 500 em POST simples)
        $middleware->web(remove: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);

        // CORS: permite o site do professor chamar nossa API de outro domínio
        $middleware->web(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Se der erro em /api/*, responde JSON (não página HTML de erro)
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'erro' => 'Erro interno do servidor.',
                ], 500);
            }
        });
    })->create();
