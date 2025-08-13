<?php

use App\Http\Controllers\admin\AcessoClienteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['role:admin'])->group(function () {
    Route::prefix('cliente')->group(function(){
        Route::get('notas-emitidas', [AcessoClienteController::class, 'notasEmitidasCliente'])->name('notas-emitidas');       

    });
    
});