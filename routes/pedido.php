<?php

use App\Http\Controllers\Admin\PedidoPneuController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::prefix('pedido')->group(function () {
        Route::post('store-pedido-pneu', [PedidoPneuController::class, 'storePedidoPneu'])->name('store-pedido-pneu');
    });

    Route::prefix('pedido-pneu')->group(function () {
        Route::get('pedido', [PedidoPneuController::class, 'index'])->name('pedido-pneus.index');

        Route::get('search-pedido-pneu', [PedidoPneuController::class, 'searchPedidoPneu'])->name('search-pedido-pneu');
    });

    Route::prefix('pedido-pneu')->group(function () {
        Route::middleware('permission:ver-pedidos-alterados-valor')->group(function () {
            Route::get('pedidos-alterados-valor', [PedidoPneuController::class, 'pedidosAlterados'])->name('pedidos-alterados-valor');
            Route::get('get-pedidos-alterados-valor', [PedidoPneuController::class, 'getPedidosAlterados'])->name('get-pedidos-alterados-valor');
            Route::post('atualizar-pedidos-alterados', [PedidoPneuController::class, 'atualizarPedidosAlterados'])->name('atualizar-pedidos-alterados');
        });
    });
});
