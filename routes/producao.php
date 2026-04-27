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
        Route::post('get-pneus-lote-pcp', [PcpProducaoController::class, 'getPneusAtrasoLotePCP'])->name('get-pneus-atraso-lote-pcp');
        Route::post('get-lote-pcp', [PcpProducaoController::class, 'getLotePCP'])->name('get-lote-pcp');
        Route::post('detalhes-pneus-lote-pcp', [PcpProducaoController::class, 'detalhesPneusLotePCP'])->name('detalhes-pneus-lote-pcp');


        Route::get('detalhes-executor', [ExecutorEtapaController::class, 'detalhesExecutor'])->name('get-details-executor');
        Route::get('get-resumo-producao-setor', [ExecutorEtapaController::class, 'resumoProducaoSetor'])->name('get-resumo-producao-setor');
    });
});
