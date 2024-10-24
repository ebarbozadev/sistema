<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usando view composer para compartilhar dados em todas as views
        View::composer('*', function ($view) {
            // Defina o array de navegações que será usado globalmente
            $navegacoes = [
                ['titulo' => 'PEDIDOS', 'url' => route('pedidos.index'), 'active' => request()->routeIs('pedidos.*')],
                ['titulo' => 'PESQUISAR', 'url' => route('pesquisar.index'), 'active' => request()->routeIs('pesquisar.*')],
                ['titulo' => 'CADASTRE-SE', 'url' => route('cadastrar.index'), 'active' => request()->routeIs('cadastrar.*')],
                ['titulo' => 'FAÇA SEU ANÚNCIO', 'url' => route('anunciar.index'), 'active' => request()->routeIs('anunciar.*')],
                ['titulo' => 'ENTRAR', 'url' => route('entrar.index'), 'active' => request()->routeIs('entrar.*')],
            ];

            // Compartilha a variável com todas as views
            $view->with('navegacoes', $navegacoes);
        });
    }
}
