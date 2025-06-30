<?php

use App\Http\Controllers\Admin\LoteExpedicaoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('expedicao')->group(function () {
        Route::get('lote-expedicao', [LoteExpedicaoController::class, 'index'])->name('lote-expedicao.index');

        Route::get('get-lote-expedicao', [LoteExpedicaoController::class, 'getLoteExpedicao'])->name('get-lote-expedicao');

        Route::get('post-create-lote-expedicao', [LoteExpedicaoController::class, 'createLoteExpedicao'])->name('post-create-lote-expedicao');

        Route::get('show-item-lote-expedicao', [LoteExpedicaoController::class, 'showItemLoteExpedicao'])->name('show-item-lote-expedicao');

        Route::get('search-ordem-producao', [LoteExpedicaoController::class, 'searchOrdemProducao'])->name('search-ordem-producao');
        Route::post('store-item-lote-expedicao', [LoteExpedicaoController::class, 'storeItemLoteExpedicao'])->name('post-store-item-lote-expedicao');
        Route::get('get-list-item-lote-expedicao', [LoteExpedicaoController::class, 'listItemLoteExpedicao'])->name('get-list-item-lote-expedicao');

        Route::post('delete-item-lote-expedicao', [LoteExpedicaoController::class, 'deleteItemLoteExpedicao'])->name('delete-item-lote-expedicao');
    });
});