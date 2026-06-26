<?php

use App\Http\Controllers\Admin\NotaDevolucaoController;
use App\Http\Controllers\Admin\NotaVendedorDivergenteController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'permission:ver-nota-devolucao'])->group(function () {
    Route::prefix('nota')->group(function () {
        Route::get('nota-devolucao', [NotaDevolucaoController::class, 'index'])->name('nota-devolucao.index');
        Route::get('get-nota-devolucao', [NotaDevolucaoController::class, 'getNotaDevolucao'])->name('get-nota-devolucao.index');
        Route::post('get-nota-devolucao-detalhes', [NotaDevolucaoController::class, 'getNotaDevolucaoDetalhes'])->name('get-nota-devolucao.detalhes');
    });
});



Route::middleware(['auth', 'permission:ver-notas-vendedor-divergente'])->group(function () {
    Route::prefix('nota')->group(function () {
        Route::get('nota-vendedor-divergentes', [NotaVendedorDivergenteController::class, 'index'])->name('nota-vendedor-divergentes.index');
        Route::get('get-nota-vendedor-divergentes', [NotaVendedorDivergenteController::class, 'getNotasVendedorDivergentes'])->name('get-nota-vendedor-divergentes');
        Route::post('substituir-item-vendedor-nota', [NotaVendedorDivergenteController::class, 'substituirItemVendedorNota'])->name('substituir-item-vendedor-nota');
        Route::post('update-alterar-vendedor-nota', [NotaVendedorDivergenteController::class, 'updateAlterarVendedorNota'])->name('update-alterar-vendedor-nota');
    });
});
