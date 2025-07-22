<?php

use App\Http\Controllers\NotaDevolucaoController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('nota')->group(function () {
        Route::get('nota-devolucao', [NotaDevolucaoController::class, 'index'])->name('nota-devolucao.index');
        Route::get('get-nota-devolucao', [NotaDevolucaoController::class, 'getNotaDevolucao'])->name('get-nota-devolucao.index');
        });
});
