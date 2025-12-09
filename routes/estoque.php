<?php

use App\Http\Controllers\Admin\EstoqueController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('estoque')->group(function () {       
        Route::get('estoque-negativo', [EstoqueController::class, 'estoqueNegativo'])->name('estoque-negativo');
        Route::get('get-estoque-negativo', [EstoqueController::class, 'getEstoqueNegativo'])->name('get-estoque-negativo');
    });

    

});