<?php

use App\Http\Controllers\Admin\RelatorioCobrancaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:ver-rel-cobranca'])->group(function () {
    Route::prefix('cobranca')->group(function(){
        Route::get('rel-cobranca', [RelatorioCobrancaController::class, 'index'])->name('rel-cobranca');
        Route::get('get-lista-cobranca', [RelatorioCobrancaController::class, 'getListCobrancaGerente'])->name('get-list-cobranca');
        Route::get('get-lista-pessoa-cobranca', [RelatorioCobrancaController::class, 'getListCobrancaPessoa'])->name('get-list-pessoa-cobranca');
        Route::get('get-lista-pessoa-cobranca-details', [RelatorioCobrancaController::class, 'getListCobrancaPessoaDetails'])->name('get-list-pessoa-cobranca-details');
        
        
        Route::get('get-cobranca-filtro', [RelatorioCobrancaController::class, 'getListCobrancaFiltro'])->name('get-cobranca-filtro');
        Route::get('get-cobranca-cnpj', [RelatorioCobrancaController::class, 'getListCobrancaFiltroCnpj'])->name('get-cobranca-filtro-cnpj');
        Route::get('get-relatorio-cobranca',[RelatorioCobrancaController::class, 'getRelatorioCobranca'])->name('get-relatorio-cobranca');
        Route::get('get-recebimento-liquidado', [RelatorioCobrancaController::class, 'getRecebimentoLiquidado'])->name('get-recebimento-liquidado');

        // rotas inadimplencias
        Route::get('rel-cliente', [RelatorioCobrancaController::class, 'relatorioFinanceiroCliente'])->name('rel-cliente');
        Route::get('get-inadimplencia', [RelatorioCobrancaController::class, 'getInadimplencia'])->name('get-inadimplencia');
        Route::get('get-inadimplencia-cliente', [RelatorioCobrancaController::class, 'getInadimplenciaDetalhes'])->name('get-inadimplencia-cliente');

        Route::get('get-limite-credito', [RelatorioCobrancaController::class, 'getLimiteCredito'])->name('get-limite-credito');
        Route::get('get-prazo-medio', [RelatorioCobrancaController::class, 'getPrazoMedio'])->name('get-prazo-medio');

        Route::get('get-list-canhoto', [RelatorioCobrancaController::class, 'getCanhoto'])->name('get-list-canhoto');
        Route::get('get-list-canhoto-details', [RelatorioCobrancaController::class, 'getCanhotoDetails'])->name('get-list-canhoto-details');
    });
    
});
