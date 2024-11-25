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
            
        });
    }
}
