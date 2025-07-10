<?php

use App\Http\Controllers\Admin\ExecutorEtapaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('producao')->group(function () {
        Route::get('producao-executor-etapa', [ExecutorEtapaController::class, 'producaoExecutorEtapa'])->name('executor-etapas.index');

        Route::get('get-producao-executor-etapa', [ExecutorEtapaController::class, 'getProducaoExecutorEtapa'])->name('get-producao-executor-etapas');
    });
});
