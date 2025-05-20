<?php

use App\Http\Controllers\Admin\FinanceiroController;
use App\Http\Controllers\Admin\LiberaOrdemFinanceiroController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:admin|cobranca'])->group(function () {
    Route::prefix('libera-ordem-financeiro')->group(function () {
        Route::get('index', [LiberaOrdemFinanceiroController::class, 'index'])->name('libera-ordem-financeiro.index');

        Route::get('get-ordem-bloqueadas-financeiro', [LiberaOrdemFinanceiroController::class, 'getListOrdemBloqueadas'])->name('get-ordens-bloqueadas-financeiro');
        Route::get('get-pneus-ordem-bloqueadas-financeiro/{id}', [LiberaOrdemFinanceiroController::class, 'getListPneusOrdemBloqueadas'])->name('get-pneus-ordens-bloqueadas-financeiro');
        Route::post('save-libera-pedido-financeiro', [LiberaOrdemFinanceiroController::class, 'saveLiberaPedido'])->name('save-libera-pedido-financeiro');
    });
});

Route::middleware(['auth', 'role:admin|financeiro'])->group(function () {
    Route::prefix('financeiro')->group(function () {        
        Route::get('libera-contas', [FinanceiroController::class, 'liberaContas'])->name('libera-contas.index');
        Route::get('get-list-contas-bloqueadas', [FinanceiroController::class, 'listContasBloqueadas'])->name('contas-bloqueadas.list');
        Route::post('get-list-contas-bloqueadas-historico', [FinanceiroController::class, 'listHistoricoContasBloqueadas'])->name('historico-contas-bloqueadas.list');
        Route::post('get-list-contas-bloqueadas-centro-resultado', [FinanceiroController::class, 'listCentroCustoContasBloqueadas'])->name('centroresultado-contas-bloqueadas.list');

        Route::post('update-status-contas-bloqueadas', [FinanceiroController::class, 'updateStatusContasBloqueadas'])->name('contas-bloqueadas.update');
    });
});
