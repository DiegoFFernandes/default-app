<?php

use App\Http\Controllers\Admin\FluxoCaixaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('fluxo-caixa')->group(function () {
        Route::middleware('can:ver-fluxo-caixa')->group(function () {
            Route::get('/', [FluxoCaixaController::class, 'index'])->name('fluxo-caixa.index');
            Route::post('salvar-lancamento', [FluxoCaixaController::class, 'salvarLancamento'])->name('fluxo-caixa.salvar-lancamento');
            Route::post('atualizar-lancamento', [FluxoCaixaController::class, 'atualizarLancamento'])->name('fluxo-caixa.atualizar-lancamento');
            Route::post('excluir-lancamento', [FluxoCaixaController::class, 'excluirLancamento'])->name('fluxo-caixa.excluir-lancamento');
        });

        Route::middleware('can:ver-fluxo-caixa-saldo-dia')->group(function () {
            Route::post('salvar-saldo-banco', [FluxoCaixaController::class, 'salvarSaldoBanco'])->name('fluxo-caixa.salvar-saldo-banco');
            Route::get('listar-saldo-banco', [FluxoCaixaController::class, 'listarSaldoBanco'])->name('fluxo-caixa.listar-saldo-banco');
            Route::post('atualizar-saldo-banco', [FluxoCaixaController::class, 'atualizarSaldoBanco'])->name('fluxo-caixa.atualizar-saldo-banco');
            Route::post('excluir-saldo-banco', [FluxoCaixaController::class, 'excluirSaldoBanco'])->name('fluxo-caixa.excluir-saldo-banco');
        });

        Route::middleware('can:config-fluxo-caixa')->group(function () {
            Route::get('listar-parametros', [FluxoCaixaController::class, 'listarParametros'])->name('fluxo-caixa.listar-parametros');
            Route::post('salvar-parametro', [FluxoCaixaController::class, 'salvarParametro'])->name('fluxo-caixa.salvar-parametro');
            Route::post('atualizar-parametro', [FluxoCaixaController::class, 'atualizarParametro'])->name('fluxo-caixa.atualizar-parametro');
            Route::post('excluir-parametro', [FluxoCaixaController::class, 'excluirParametro'])->name('fluxo-caixa.excluir-parametro');

            Route::get('listar-compensacao', [FluxoCaixaController::class, 'listarCompensacao'])->name('fluxo-caixa.listar-compensacao');
            Route::post('salvar-compensacao', [FluxoCaixaController::class, 'salvarCompensacao'])->name('fluxo-caixa.salvar-compensacao');
            Route::post('atualizar-compensacao', [FluxoCaixaController::class, 'atualizarCompensacao'])->name('fluxo-caixa.atualizar-compensacao');
            Route::post('excluir-compensacao', [FluxoCaixaController::class, 'excluirCompensacao'])->name('fluxo-caixa.excluir-compensacao');

            Route::post('salvar-origem-saldo-banco', [FluxoCaixaController::class, 'salvarOrigemSaldoBanco'])->name('fluxo-caixa.salvar-origem-saldo-banco');

            Route::get('listar-saldo-conta', [FluxoCaixaController::class, 'listarSaldoConta'])->name('fluxo-caixa.listar-saldo-conta');
            Route::post('salvar-saldo-conta', [FluxoCaixaController::class, 'salvarSaldoConta'])->name('fluxo-caixa.salvar-saldo-conta');
            Route::post('excluir-saldo-conta', [FluxoCaixaController::class, 'excluirSaldoConta'])->name('fluxo-caixa.excluir-saldo-conta');
            Route::get('buscar-contas-saldo-firebird', [FluxoCaixaController::class, 'buscarContasSaldoFirebird'])->name('fluxo-caixa.buscar-contas-saldo-firebird');
        });
    });
});
