<?php

use App\Http\Controllers\Admin\EstoqueController;
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


        //Medidas de pneus
        Route::get('search-medidas-pneu', [EstoqueController::class, 'searchMedidasPneu'])->name('search-medidas-pneus');
        Route::get('search-modelo-pneu', [EstoqueController::class, 'searchModeloPneu'])->name('search-modelo-pneus');
        
    });

    

});