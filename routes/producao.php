<?php

use App\Http\Controllers\Admin\ExecutorEtapaController;
use App\Http\Controllers\Admin\PcpProducaoController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('producao')->group(function () {
        Route::get('producao-executor-etapa', [ExecutorEtapaController::class, 'producaoExecutorEtapa'])->name('executor-etapas.index');
        Route::get('get-producao-executor-etapa', [ExecutorEtapaController::class, 'getProducaoExecutorEtapa'])->name('get-producao-executor-etapas');


        Route::get('detalhes-executor', [ExecutorEtapaController::class, 'detalhesExecutor'])->name('get-details-executor');
        Route::get('get-resumo-producao-setor', [ExecutorEtapaController::class, 'resumoProducaoSetor'])->name('get-resumo-producao-setor');

        Route::get('get-executor-etapa', [ExecutorEtapaController::class, 'getExecutorEtapa'])->name('get-executor-etapa');

        Route::prefix('pcp')->group(function () {
            Route::get('pneus-lote-pcp', [PcpProducaoController::class, 'pneusLotePCP'])->name('pneus-lote-pcp');
            Route::post('get-pneus-lote-pcp', [PcpProducaoController::class, 'getPneusAtrasoLotePCP'])->name('get-pneus-atraso-lote-pcp');
            Route::post('get-lote-pcp', [PcpProducaoController::class, 'getLotePCP'])->name('get-lote-pcp');
            Route::post('detalhes-pneus-lote-pcp', [PcpProducaoController::class, 'detalhesPneusLotePCP'])->name('detalhes-pneus-lote-pcp');
            Route::post('consumo-estoque-lote-materia-prima', [PcpProducaoController::class, 'consumoEstoqueLoteMateriaPrima'])->name('consumo-estoque-lote-materia-prima');

            Route::post('bandas-sem-associacao', [PcpProducaoController::class, 'bandasSemAssociacao'])->name('bandas-sem-associacao');

            Route::get('get-controle-lote-pcp', [PcpProducaoController::class, 'getControleLotePCP'])->name('get-controle-lote-pcp');
            Route::post('removerOrdemProducaoLotePCP', [PcpProducaoController::class, 'removerOrdemProducaoLotePCP'])->name('remover-ordem-producao-lote-pcp');
        
        
            Route::post('salvar-lote-pcp', [PcpProducaoController::class, 'salvarLotePCP'])->name('salvar-lote-pcp');
            Route::get('get-lote-pcp-em-producao', [PcpProducaoController::class, 'getListLotePCPEmProducao'])->name('get-lote-pcp-em-producao');
            Route::post('atualiza-lote-pneus-lote-pcp', [PcpProducaoController::class, 'atualizaLotePneusLotePCP'])->name('atualiza-lote-pneus-lote-pcp');
            
            Route::get('get-list-pneus-lote-sem-pcp', [PcpProducaoController::class, 'getListPneusLoteSemPCP'])->name('get-list-pneus-lote-sem-pcp');
            Route::get('get-list-pedidos-sem-pcp', [PcpProducaoController::class, 'getListPedidosSemPCP'])->name('get-list-pedidos-sem-pcp');
            Route::get('get-list-ordens-producao-sem-pcp', [PcpProducaoController::class, 'getListOrdensProducaoSemPCP'])->name('get-list-ordens-producao-sem-pcp');

            Route::post('salvar-pneus-lote-pcp', [PcpProducaoController::class, 'salvarPneusLotePCP'])->name('salvar-pneus-lote-pcp');
            });
    });
});
