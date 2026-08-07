<?php

namespace App\Providers;

use App\Models\CompraSubgrupo;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Lista de subgrupos disponível no modal de item (carrega só quando o modal é renderizado).
        View::composer('admin.compras.itens.modal-item', function ($view) {
            $view->with('subgruposCompra', (new CompraSubgrupo())->getAll());
        });
    }
}
