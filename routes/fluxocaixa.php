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

        Route::get('listar-parametros', [FluxoCaixaController::class, 'listarParametros'])->name('fluxo-caixa.listar-parametros');
        Route::post('salvar-parametro', [FluxoCaixaController::class, 'salvarParametro'])->name('fluxo-caixa.salvar-parametro');
        Route::post('atualizar-parametro', [FluxoCaixaController::class, 'atualizarParametro'])->name('fluxo-caixa.atualizar-parametro');
        Route::post('excluir-parametro', [FluxoCaixaController::class, 'excluirParametro'])->name('fluxo-caixa.excluir-parametro');

        Route::get('listar-compensacao', [FluxoCaixaController::class, 'listarCompensacao'])->name('fluxo-caixa.listar-compensacao');
        Route::post('salvar-compensacao', [FluxoCaixaController::class, 'salvarCompensacao'])->name('fluxo-caixa.salvar-compensacao');
        Route::post('atualizar-compensacao', [FluxoCaixaController::class, 'atualizarCompensacao'])->name('fluxo-caixa.atualizar-compensacao');
        Route::post('excluir-compensacao', [FluxoCaixaController::class, 'excluirCompensacao'])->name('fluxo-caixa.excluir-compensacao');
    });
});
