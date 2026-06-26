<?php

use App\Http\Controllers\Admin\IAController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('ia')->group(function () {
    Route::get('index', [IAController::class, 'index'])->name('ia-index');
    Route::post('perguntar', [IAController::class, 'perguntar'])->name('ia-perguntar');
});

Route::middleware(['auth', 'permission:solicitacao-recursos-ia'])->prefix('ia')->group(function () {
    Route::post('resumo', [IAController::class, 'resumo'])->name('ia-resumo');
    Route::post('resumo-whatsapp', [IAController::class, 'resumoWhatsapp'])->name('ia-resumo-whatsapp');
});
