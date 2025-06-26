<?php

use App\Http\Controllers\Admin\LoteExpedicaoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('expedicao')->group(function () {
        Route::get('lote-expedicao', [LoteExpedicaoController::class, 'index'])->name('lote-expedicao.index');

        Route::get('get-lote-expedicao', [LoteExpedicaoController::class, 'getLoteExpedicao'])->name('get-lote-expedicao');

        Route::get('post-create-lote-expedicao', [LoteExpedicaoController::class, 'createLoteExpedicao'])->name('post-create-lote-expedicao');
    });
});