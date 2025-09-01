<?php

use App\Http\Controllers\admin\AcessoClienteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['role:admin|cliente'])->group(function () {
    Route::prefix('cliente')->group(function () {
        Route::get('dados-cliente', [AcessoClienteController::class, 'listNotasEmitidasCliente'])->name('list-notas-emitidas');
        Route::get('get-list-nota-emitida', [AcessoClienteController::class, 'getListNotasEmitidasCliente'])->name('get-list-nota-emitida');
        Route::get('get-layout-nota-emitida/{id}', [AcessoClienteController::class, 'layoutNotaEmitidaCliente'])->name('get-layout-nota-emitida');

        Route::get('get-listar-boletos-emitidos', [AcessoClienteController::class, 'getListBoletosEmitidosCliente'])->name('get-listar-boletos-emitidos');
        Route::get('get-layout-boleto-emitida', [AcessoClienteController::class, 'layoutBoletoEmitidoCliente'])->name('get-layout-boleto-emitida');

    });
});
