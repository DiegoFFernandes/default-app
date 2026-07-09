<?php

use App\Http\Controllers\Admin\FluxoCaixaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('fluxo-caixa')->group(function () {
        Route::get('/', [FluxoCaixaController::class, 'index'])->name('fluxo-caixa.index');
    });
});
