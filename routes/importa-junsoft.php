<?php

use App\Http\Controllers\Admin\ImportaJunsoftController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('importa-junsoft')->group(function () {
        Route::get('index', [ImportaJunsoftController::class, 'index'])->name('importa.index');
        Route::get('save-item-marca-ajax', [ImportaJunsoftController::class, 'AjaxImportaItem'])->name('importa-item.index');        
    });
});
