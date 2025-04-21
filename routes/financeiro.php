<?php

use App\Http\Controllers\Admin\AreaComercialController;
use App\Http\Controllers\Admin\BloqueioPedidosController;
use App\Http\Controllers\Admin\LiberaOrdemFinanceiroController;
use App\Http\Controllers\Admin\RegiaoComercialController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:admin|cobranca'])->group(function () {
    Route::prefix('libera-ordem-financeiro')->group(function () {
        Route::get('index', [LiberaOrdemFinanceiroController::class, 'index'])->name('libera-ordem-financeiro.index');

        Route::get('get-ordem-bloqueadas-financeiro', [LiberaOrdemFinanceiroController::class, 'getListOrdemBloqueadas'])->name('get-ordens-bloqueadas-financeiro');
        Route::get('get-pneus-ordem-bloqueadas-financeiro/{id}', [LiberaOrdemFinanceiroController::class, 'getListPneusOrdemBloqueadas'])->name('get-pneus-ordens-bloqueadas-financeiro');
        Route::post('save-libera-pedido-financeiro', [LiberaOrdemFinanceiroController::class, 'saveLiberaPedido'])->name('save-libera-pedido-financeiro');
    });
});
