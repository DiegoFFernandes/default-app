<?php

use App\Http\Controllers\Admin\ExecutorEtapaController;
use App\Http\Controllers\Admin\PcpProducaoController;
use App\Http\Controllers\Admin\ProducaoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('producao')->group(function () {
        Route::get('producao-executor-etapa', [ExecutorEtapaController::class, 'producaoExecutorEtapa'])->name('executor-etapas.index');
        Route::get('get-producao-executor-etapa', [ExecutorEtapaController::class, 'getProducaoExecutorEtapa'])->name('get-producao-executor-etapas');
        Route::get('pneus-lote-pcp', [PcpProducaoController::class, 'pneusLotePCP'])->name('pneus-lote-pcp');
        Route::post('get-pneus-lote-pcp', [PcpProducaoController::class, 'getPneusLotePCP'])->name('get-pneus-lote-pcp');
        Route::post('get-lote-pcp', [PcpProducaoController::class, 'getLotePCP'])->name('get-lote-pcp');
    });
});
