<?php

use App\Http\Controllers\Admin\EstoqueController;
use App\Http\Controllers\Admin\ItemLoteEstoqueController;
use App\Http\Controllers\Admin\LoteEstoqueController;
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
        Route::get('index', [LoteEstoqueController::class, 'index'])->name('entrada-estoque.index');
        Route::get('cria-lote', [LoteEstoqueController::class, 'store'])->name('estoque.cria-lote');
        Route::get('get-lotes', [LoteEstoqueController::class, 'getLotes'])->name('estoque.get-lotes');
        Route::post('finaliza-lote', [LoteEstoqueController::class, 'finishLote'])->name('estoque.finish-lote');
        Route::delete('delete-lote', [LoteEstoqueController::class, 'delete'])->name('estoque.delete-lote');


        Route::get('add-item-lote/{id}', [ItemLoteEstoqueController::class, 'index'])->name('add-item-lote.index');
        Route::get('get-busca-item/{cd_barras}', [ItemLoteEstoqueController::class, 'getBuscaItem'])->name('get-item-lote');
        Route::get('get-itens-lote', [ItemLoteEstoqueController::class, 'getItensLote'])->name('estoque.get-itens-lote');
        Route::get('get-resume-itens-lote', [ItemLoteEstoqueController::class, 'getResumeItens'])->name('estoque.get-resume-itens-lote');

        
        Route::post('add-item-lote/store', [ItemLoteEstoqueController::class, 'store'])->name('add-item-lote.store');
        Route::delete('delete-item-lote', [ItemLoteEstoqueController::class, 'delete'])->name('delete-item-lote');

        Route::get('item-lote-fechado/{id}', [ItemLoteEstoqueController::class, 'listItemLote'])->name('item-lote-fechado');
    });
});
