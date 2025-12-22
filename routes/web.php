<?php

use App\Http\Controllers\Admin\FormaPagmentoController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PedidoPneuController;
use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/clear-cache-all', function () {

    Artisan::call('cache:clear');

    dd("Cache Clear All");
});

Auth::routes();

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');


Route::get('/home', [HomeController::class, 'index'])->name('home');


Route::middleware('auth')->group(function () {
    Route::prefix('formas-pagamento')->group(function () {
        Route::get('get-cond-pagamento', [FormaPagmentoController::class, 'condicaoPagamento'])->name('get-cond-pagamento');  
        Route::get('get-form-pagamento', [FormaPagmentoController::class, 'formaPagamento'])->name('get-form-pagamento');       
    });

    Route::prefix('produto')->group(function () {
        Route::get('get-servico-pneu-medida', [ItemController::class, 'servicoPneu'])->name('get-servico-pneu-medida');       
    });


    Route::prefix('pedido')->group(function () {
        Route::get('store-pedido-pneu', [PedidoPneuController::class, 'storePedidoPneu'])->name('store-pedido-pneu');       
    });
});