<?php

use App\Http\Controllers\Admin\PedidoPneuController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::prefix('pedido')->group(function () {
        Route::post('store-pedido-pneu', [PedidoPneuController::class, 'storePedidoPneu'])->name('store-pedido-pneu');
    });

    Route::prefix('pedido-pneus')->group(function () {
        Route::get('pedido', [PedidoPneuController::class, 'index'])->name('pedido-pneus.index');

        Route::get('search-pedido-pneu', [PedidoPneuController::class, 'searchPedidoPneu'])->name('search-pedido-pneu');    
    });
});
