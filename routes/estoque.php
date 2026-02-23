<?php

use App\Http\Controllers\Admin\EstoqueController;
use App\Http\Controllers\admin\ItemLoteEntradaEstoqueController;
use App\Http\Controllers\Admin\LoteEntradaEstoqueController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:ver-estoque'])->group(function () {
    Route::prefix('estoque')->group(function () {
        Route::get('estoque-negativo', [EstoqueController::class, 'estoqueNegativo'])->name('estoque-negativo');
        Route::get('get-estoque-negativo', [EstoqueController::class, 'getEstoqueNegativo'])->name('get-estoque-negativo');


        //Carcaças da casa
        Route::get('carcacas-da-casa', [EstoqueController::class, 'carcacaCasa'])->name('carcaca-casa');
        Route::get('get-carcacas-da-casa', [EstoqueController::class, 'getCarcacaCasa'])->name('get-carcaca-casa');
        Route::post('store-carcaca', [EstoqueController::class, 'storeCarcaca'])->name('store-carcaca');
        Route::get('edit-carcaca', [EstoqueController::class, 'editCarcaca'])->name('edit-carcaca');
        Route::post('delete-carcaca', [EstoqueController::class, 'deleteCarcaca'])->name('delete-carcaca');
        Route::post('transfer-carcaca', [EstoqueController::class, 'transferCarcaca'])->name('transfer-carcaca');


        //Carcacas baixadas
        Route::get('get-carcacas-baixadas', [EstoqueController::class, 'getCarcacaCasaBaixas'])->name('get-carcaca-casa-baixas');

        //Carcasas prontas
        Route::get('get-carcacas-prontas', [EstoqueController::class, 'getCarcacaCasaProntas'])->name('get-carcaca-casa-prontas');


        //Medidas de pneus
        Route::get('search-medidas-pneu', [EstoqueController::class, 'searchMedidasPneu'])->name('search-medidas-pneus');
        Route::get('search-modelo-pneu', [EstoqueController::class, 'searchModeloPneu'])->name('search-modelo-pneus');
    });

    Route::prefix('estoque-entrada')->group(function () {
        Route::get('index', [LoteEntradaEstoqueController::class, 'index'])->name('estoque.index');
        Route::post('cria-lote', [LoteEntradaEstoqueController::class, 'store'])->name('estoque.cria-lote');
        Route::get('get-lotes', [LoteEntradaEstoqueController::class, 'getLotes'])->name('estoque.get-lotes');
        Route::post('finaliza-lote', [LoteEntradaEstoqueController::class, 'finishLote'])->name('estoque.finish-lote');
        Route::delete('delete-lote', [LoteEntradaEstoqueController::class, 'delete'])->name('estoque.delete-lote');


        Route::get('add-item-lote/{id}', [ItemLoteEntradaEstoqueController::class, 'index'])->name('add-item-lote.index');
        Route::get('get-busca-item/{cd_barras}', [ItemLoteEntradaEstoqueController::class, 'getBuscaItem'])->name('get-item-lote');
        Route::get('get-itens-lote', [ItemLoteEntradaEstoqueController::class, 'getItensLote'])->name('estoque.get-itens-lote');
        Route::get('get-resume-itens-lote', [ItemLoteEntradaEstoqueController::class, 'getResumeItens'])->name('estoque.get-resume-itens-lote');

        Route::get('get-busca-item',  function () {
            return;
        });
        Route::post('add-item-lote/store', [ItemLoteEntradaEstoqueController::class, 'store'])->name('add-item-lote.store');
        Route::delete('delete-item-lote', [ItemLoteEntradaEstoqueController::class, 'delete'])->name('delete-item-lote');

        Route::get('item-lote-fechado/{id}', [ItemLoteEntradaEstoqueController::class, 'listItemLote'])->name('item-lote-fechado');
    });
});
