<?php

namespace App\Providers;

use App\Models\CompraParam;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Itens próprios: só quando a fonte de itens é 'P' e o usuário mexe em compras.
        // Usado na sidebar, no botão "Novo Item" e nas rotas do catálogo próprio.
        Gate::define('compra-itens-proprios', function ($user) {
            return (new CompraParam())->usaItensProprios()
                && $user->can('solicitacao-compra-gerenciar');
        });
    }
}
