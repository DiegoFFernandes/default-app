<?php

use App\Http\Controllers\Admin\RelatorioCobrancaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:ver-rel-cobranca'])->group(function () {
    Route::prefix('cobranca')->group(function(){
        Route::get('rel-cobranca', [RelatorioCobrancaController::class, 'index'])->name('rel-cobranca');
        Route::get('get-lista-cobranca', [RelatorioCobrancaController::class, 'getListCobranca'])->name('get-list-cobranca');
        Route::get('get-lista-pessoa-cobranca', [RelatorioCobrancaController::class, 'getListCobrancaPessoa'])->name('get-list-pessoa-cobranca');
        Route::get('get-lista-pessoa-cobranca-details', [RelatorioCobrancaController::class, 'getListCobrancaPessoaDetails'])->name('get-list-pessoa-cobranca-details');
        
        
        Route::get('get-cobranca-filtro', [RelatorioCobrancaController::class, 'getListCobrancaFiltro'])->name('get-cobranca-filtro');
        Route::get('get-cobranca-cnpj', [RelatorioCobrancaController::class, 'getListCobrancaFiltroCnpj'])->name('get-cobranca-filtro-cnpj');

        Route::get('teste-cobranca',[RelatorioCobrancaController::class, 'testeCobranca'])->name('teste-cobranca');

    });
    
});
