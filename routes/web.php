<?php

use App\Http\Controllers\admin\PessoaController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\FormaPagmentoController;
use App\Http\Controllers\Admin\HistoricoController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PedidoPneuController;
use App\Http\Controllers\Admin\TipoContaController;
use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/manifest.json', function () {
    return response()->json([
        'name'             => config('app.name'),
        'short_name'       => config('pwa.short_name'),
        'description'      => 'Sistema de gestão ' . config('app.name'),
        'start_url'        => '/',
        'scope'            => '/',
        'display'          => 'standalone',
        'orientation'      => 'portrait-primary',
        'background_color' => config('pwa.background_color'),
        'theme_color'      => config('pwa.theme_color'),
        'icons'            => [
            ['src' => '/img/android-chrome-192x192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => '/img/android-chrome-512x512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => '/img/apple-touch-icon.png',       'sizes' => '180x180', 'type' => 'image/png'],
        ],
    ], 200, ['Content-Type' => 'application/manifest+json']);
});


Route::get('/clear-cache-all', function () {

    Artisan::call('cache:clear');

    dd("Cache Clear All");
});

Auth::routes();

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');


Route::get('/home', [HomeController::class, 'index'])->name('home');


Route::middleware('auth')->group(function () {
    Route::prefix('firebird')->group(function () {
        Route::get('empresas',    [EmpresaController::class,   'index'])->name('firebird.empresas');
        Route::get('tipos-conta', [TipoContaController::class, 'index'])->name('firebird.tipos-conta');
        Route::get('historicos',  [HistoricoController::class, 'index'])->name('firebird.historicos');
    });

    Route::prefix('formas-pagamento')->group(function () {
        Route::get('get-cond-pagamento', [FormaPagmentoController::class, 'condicaoPagamento'])->name('get-cond-pagamento');
        Route::get('get-form-pagamento', [FormaPagmentoController::class, 'formaPagamento'])->name('get-form-pagamento');
    });

    Route::prefix('produto')->group(function () {
        Route::get('get-servico-pneu-medida', [ItemController::class, 'servicoPneu'])->name('get-servico-pneu-medida');
        Route::get('search-produto', [ItemController::class, 'searchProduto'])->name('search-product');
    });

    Route::get('search-pessoas', [PessoaController::class, 'searchPessoas'])->name('pessoa.search');
});
