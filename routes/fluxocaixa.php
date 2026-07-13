<?php

use App\Http\Controllers\Admin\FluxoCaixaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('fluxo-caixa')->group(function () {
        Route::get('/', [FluxoCaixaController::class, 'index'])->name('fluxo-caixa.index');
        Route::post('salvar-saldo-banco', [FluxoCaixaController::class, 'salvarSaldoBanco'])->name('fluxo-caixa.salvar-saldo-banco');
        Route::get('listar-saldo-banco', [FluxoCaixaController::class, 'listarSaldoBanco'])->name('fluxo-caixa.listar-saldo-banco');
        Route::post('atualizar-saldo-banco', [FluxoCaixaController::class, 'atualizarSaldoBanco'])->name('fluxo-caixa.atualizar-saldo-banco');
        Route::post('excluir-saldo-banco', [FluxoCaixaController::class, 'excluirSaldoBanco'])->name('fluxo-caixa.excluir-saldo-banco');
    });
});
