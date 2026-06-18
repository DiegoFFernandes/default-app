<?php

use App\Http\Controllers\Admin\DespesaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:ver-despesas'])->group(function () {

    Route::prefix('despesa')->group(function () {

        Route::get('/',                  [DespesaController::class, 'index'])->name('despesa.index');
        Route::post('store',             [DespesaController::class, 'store'])->name('despesa.store');
        Route::get('get-comprovantes',   [DespesaController::class, 'getComprovantes'])->name('despesa.get');
        Route::get('veiculos',           [DespesaController::class, 'searchVeiculos'])->name('despesa.veiculos');
        Route::put('{id}',               [DespesaController::class, 'update'])->name('despesa.update')->whereNumber('id');
        Route::post('{id}/toggle-visto', [DespesaController::class, 'toggleVisto'])->name('despesa.toggle-visto')->whereNumber('id');
    });
});
