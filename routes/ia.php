<?php

use App\Http\Controllers\Admin\IAController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('ia')->group(function () {
        Route::get('index', [IAController::class, 'index'])->name('ia-index');
        Route::post('perguntar', [IAController::class, 'perguntar'])->name('ia-perguntar');
    });
});
