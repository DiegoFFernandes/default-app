<?php

use App\Http\Controllers\Admin\ImportaJunsoftController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('importar-dados')->group(function () {
        Route::get('index', [ImportaJunsoftController::class, 'index'])->name('importar.index');
        Route::get('save-item-marca-ajax', [ImportaJunsoftController::class, 'AjaxImportaItem'])->name('importar-item.index');        
    });
});
