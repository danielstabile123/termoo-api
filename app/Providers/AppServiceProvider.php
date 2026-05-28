<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Provider = lugar onde registramos serviços globais da aplicação.
 *
 * Executado quando o Laravel sobe. Aqui dizemos como criar o TermooService
 * e qual idioma usar nas mensagens de validação.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * register() roda cedo: define como o Laravel instancia classes.
     */
    public function register(): void
    {
        // singleton = uma única instância do TermooService por requisição
        $this->app->singleton(\App\Services\TermooService::class);
    }

    /**
     * boot() roda depois: configurações finais antes de atender HTTP.
     */
    public function boot(): void
    {
        // Mensagens de erro de validação em português
        app()->setLocale('pt_BR');
    }
}
