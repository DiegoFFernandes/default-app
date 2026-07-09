<?php

use App\Http\Controllers\admin\FaturamentoController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'permission:ver-analise-faturamento'])->group(function () {
    Route::prefix('faturamento')->group(function () {
        Route::get('analise-faturamento', [FaturamentoController::class, 'index'])->name('analise-faturamento.index');

        Route::get('get-analise-faturamento', [FaturamentoController::class, 'getAnaliseFaturamento'])->name('get-analise-faturamento.index');
        });
});
