<?php

use App\Http\Controllers\Admin\AprovacaoComprasController;
use App\Http\Controllers\Admin\CotacaoComprasController;
use App\Http\Controllers\Admin\ConfigComprasController;
use App\Http\Controllers\Admin\SolicitacaoComprasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::prefix('compras')->group(function () {

        // AJAX search (Select2)
        Route::get('search-item-compra',      [SolicitacaoComprasController::class, 'searchItem'])->name('compras.search-item');
        Route::get('search-fornecedor-compra', [SolicitacaoComprasController::class, 'searchFornecedor'])->name('compras.search-fornecedor');

        // Solicitações
        Route::get('solicitacoes',                [SolicitacaoComprasController::class, 'index'])->name('compras.solicitacoes.index');
        Route::get('get-solicitacoes',            [SolicitacaoComprasController::class, 'list'])->name('compras.solicitacoes.list');
        Route::get('solicitacoes/nova',           [SolicitacaoComprasController::class, 'create'])->name('compras.solicitacoes.create');
        Route::post('solicitacoes',               [SolicitacaoComprasController::class, 'store'])->name('compras.solicitacoes.store');
        Route::get('solicitacoes/{id}',           [SolicitacaoComprasController::class, 'show'])->name('compras.solicitacoes.show')->whereNumber('id');
        Route::get('solicitacoes/{id}/editar',    [SolicitacaoComprasController::class, 'edit'])->name('compras.solicitacoes.edit')->whereNumber('id');
        Route::post('solicitacoes/{id}/update',   [SolicitacaoComprasController::class, 'update'])->name('compras.solicitacoes.update')->whereNumber('id');
        Route::delete('solicitacoes/{id}',        [SolicitacaoComprasController::class, 'destroy'])->name('compras.solicitacoes.destroy')->whereNumber('id');
        Route::post('solicitacoes/{id}/submeter',       [SolicitacaoComprasController::class, 'submeter'])->name('compras.solicitacoes.submeter')->whereNumber('id');
        Route::get('solicitacoes/{id}/exportar-excel',  [SolicitacaoComprasController::class, 'exportarExcel'])->name('compras.solicitacoes.exportar-excel')->whereNumber('id');

        // Itens
        Route::get('get-itens/{idSolicitacao}', [SolicitacaoComprasController::class, 'listItens'])->name('compras.itens.list');
        Route::post('itens',                    [SolicitacaoComprasController::class, 'storeItem'])->name('compras.itens.store');
        Route::delete('itens/{id}',             [SolicitacaoComprasController::class, 'destroyItem'])->name('compras.itens.destroy');

        // Cotações
        Route::get('get-cotacoes/{idSolicitacao}',    [CotacaoComprasController::class, 'list'])->name('compras.cotacoes.list');
        Route::post('cotacoes',                       [CotacaoComprasController::class, 'store'])->name('compras.cotacoes.store');
        Route::post('cotacoes/{id}/update',           [CotacaoComprasController::class, 'update'])->name('compras.cotacoes.update');
        Route::delete('cotacoes/{id}',                [CotacaoComprasController::class, 'destroy'])->name('compras.cotacoes.destroy');
        Route::post('cotacoes/selecionar-fornecedor', [CotacaoComprasController::class, 'selecionarFornecedor'])->name('compras.cotacoes.selecionar');

        // Aprovações
        Route::get('aprovacoes',          [AprovacaoComprasController::class, 'index'])->name('compras.aprovacoes.index');
        Route::get('get-aprovacoes',      [AprovacaoComprasController::class, 'list'])->name('compras.aprovacoes.list');
        Route::post('aprovacoes/aprovar', [AprovacaoComprasController::class, 'aprovar'])->name('compras.aprovacoes.aprovar');
        Route::post('aprovacoes/reprovar',[AprovacaoComprasController::class, 'reprovar'])->name('compras.aprovacoes.reprovar');

        // Configuração
        Route::get('configuracao',               [ConfigComprasController::class, 'index'])->name('compras.configuracao.index');
        Route::get('get-faixas',                 [ConfigComprasController::class, 'listFaixas'])->name('compras.configuracao.list-faixas');
        Route::post('faixas',                    [ConfigComprasController::class, 'storeFaixa'])->name('compras.configuracao.store-faixa');
        Route::post('faixas/{id}/update',        [ConfigComprasController::class, 'updateFaixa'])->name('compras.configuracao.update-faixa');
        Route::delete('faixas/{id}',             [ConfigComprasController::class, 'destroyFaixa'])->name('compras.configuracao.destroy-faixa');
        Route::get('faixas/{id}/aprovadores',    [ConfigComprasController::class, 'listAprovadores'])->name('compras.configuracao.list-aprovadores');
        Route::post('aprovadores',               [ConfigComprasController::class, 'storeAprovador'])->name('compras.configuracao.store-aprovador');
        Route::post('aprovadores/reordenar',     [ConfigComprasController::class, 'reordenarAprovadores'])->name('compras.configuracao.reordenar-aprovadores');
        Route::delete('aprovadores/{id}',        [ConfigComprasController::class, 'destroyAprovador'])->name('compras.configuracao.destroy-aprovador');
    });
});
