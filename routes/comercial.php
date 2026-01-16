<?php

use App\Http\Controllers\Admin\AreaComercialController;
use App\Http\Controllers\Admin\BloqueioPedidosController;
use App\Http\Controllers\admin\GarantiaController;
use App\Http\Controllers\Admin\LiberaOrdemComissaoController;
use App\Http\Controllers\Admin\ProducaoController;
use App\Http\Controllers\Admin\RegiaoComercialController;
use App\Http\Controllers\Admin\SupervisorComercialController;
use App\Http\Controllers\Admin\VendedorController;
use App\Http\Controllers\Admin\ColetaController;
use App\Http\Controllers\Admin\ComissaoController;
use App\Http\Controllers\Admin\GerenteUnidadeController;
use App\Http\Controllers\admin\TabelaPrecoController;
use App\Http\Controllers\Admin\VendedorBorrachariaController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'permission:ver-libera-ordem-comercial'])->group(function () {
    Route::prefix('libera-ordem-comercial')->group(function () {
        Route::get('index', [LiberaOrdemComissaoController::class, 'index'])->name('libera-ordem-comissao.index');

        Route::get('get-ordem-bloqueadas-comercial', [LiberaOrdemComissaoController::class, 'getListOrdemBloqueadas'])->name('get-ordens-bloqueadas-comercial');
        Route::get('get-pneus-ordem-bloqueadas-comercial/{id}', [LiberaOrdemComissaoController::class, 'getListPneusOrdemBloqueadas'])->name('get-pneus-ordens-bloqueadas-comercial');
        Route::post('save-libera-pedido', [LiberaOrdemComissaoController::class, 'saveLiberaPedido'])->name('save-libera-pedido');
        Route::get('get-calcula-comissao', [LiberaOrdemComissaoController::class, 'getCalculaComissao'])->name('get-calcula-comissao');
        Route::get('libera-abaixo-desconto', [LiberaOrdemComissaoController::class, 'liberaAbaixoDesconto'])->name('libera-abaixo-desconto');

    });
});

Route::middleware(['auth', 'role:admin|gerente comercial|supervisor|usuario comercial'])->group(function () {
    Route::prefix('tabela')->group(function () {
        Route::get('tabela-preco', [TabelaPrecoController::class, 'index'])->name('tabela-preco.index');
        Route::get('get-tabela-preco', [TabelaPrecoController::class, 'getTabPreco'])->name('get-tabela-preco');
        Route::get('get-item-tabela-preco', [TabelaPrecoController::class, 'getItemTabPreco'])->name('get-item-tabela-preco');

        Route::get('get-tabela-cliente-preco', [TabelaPrecoController::class, 'getTabClientePreco'])->name('get-tabela-cliente-preco');
        Route::get('get-search-medida', [TabelaPrecoController::class, 'getSearchMedida'])->name('get-search-medida');

        // Manchão Agrícola e OTR e Vulcanização
        Route::get('get-search-adicional', [TabelaPrecoController::class, 'getSearchAdicional'])->name('get-search-adicional');
        Route::get('get-previa-tabela-preco', [TabelaPrecoController::class, 'getPreviaTabelaPreco'])->name('get-previa-tabela-preco');
        Route::get('get-verifica-tabela-cadastrada', [TabelaPrecoController::class, 'getVerificaExistsTabelaCadastrada'])->name('get-verifica-tabela-cadastrada');

        //Salva os itens na tabela temporária para importação
        Route::post('salva-item-tabela-preco', [TabelaPrecoController::class, 'salvaItemTabelaPreco'])->name('salva-item-tabela-preco');

        // Importa os itens da tabela temporária para a tabela oficial
        Route::get('get-tabela-preco-preview', [TabelaPrecoController::class, 'getTabPrecoPreview'])->name('get-tabela-preco-preview');
    });
});

Route::middleware(['auth', 'role:admin|gerente comercial'])->group(function () {
    Route::prefix('tabela')->group(function () {        
        Route::post('get-importar-tabela-preco', [TabelaPrecoController::class, 'importarTabelaPreco'])->name('importar-tabela-preco');
        Route::post('vincular-tabela-preco', [TabelaPrecoController::class, 'vincularTabelaPreco'])->name('vincular-tabela-preco');
        Route::post('deletar-tabela-preco', [TabelaPrecoController::class, 'deletarTabelaPreco'])->name('deletar-tabela-preco');
        Route::post('cancelar-vinculo', [TabelaPrecoController::class, 'cancelarVinculo'])->name('cancelar-vinculo');
        Route::get('divergencia-tabela-preco', [TabelaPrecoController::class, 'divergenciaTabelaPreco'])->name('divergencia-tabela-preco');
    });
});

Route::middleware(['auth', 'permission:ver-produzidos-sem-faturar'])->group(function () {
    Route::prefix('producao')->group(function () {
        Route::get('pneus-produzidos-sem-faturar', [ProducaoController::class, 'index'])->name('produzidos-sem-faturar');
        Route::get('get-pneus-produzidos-sem-faturar', [ProducaoController::class, 'getListPneusProduzidosFaturar'])->name('get-pneus-produzidos-sem-faturar');
        Route::get('get-pneus-produzidos-sem-faturar-details', [ProducaoController::class, 'getListPneusProduzidosFaturarDetails'])->name('get-pneus-produzidos-sem-faturar-details');
    });
});


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('cadastro')->group(function () {

        Route::get('gerente-comercial', [AreaComercialController::class, 'index'])->name('gerente-comercial.index');
        Route::get('get-area-comercial', [AreaComercialController::class, 'create'])->name('get-area-comercial.create');
        Route::get('get-table-area-usuario', [AreaComercialController::class, 'list'])->name('get-table-area-usuario');
        Route::post('edit-area-usuario', [AreaComercialController::class, 'update'])->name('edit-area-usuario');
        Route::delete('area-usuario-delete', [AreaComercialController::class, 'destroy'])->name('area-usuario.delete');

        Route::get('regiao-comercial', [RegiaoComercialController::class, 'index'])->name('regiao-comercial.index');
        Route::get('get-regiao-comercial', [RegiaoComercialController::class, 'create'])->name('get-regiao-comercial.create');
        Route::get('get-table-regiao-usuario', [RegiaoComercialController::class, 'list'])->name('get-table-regiao-usuario');
        Route::post('edit-regiao-usuario', [RegiaoComercialController::class, 'update'])->name('edit-regiao-usuario');
        Route::delete('regiao-usuario-delete', [RegiaoComercialController::class, 'destroy'])->name('regiao-usuario.delete');


        Route::get('supervisor-comercial', [SupervisorComercialController::class, 'index'])->name('supervisor-comercial.index');
        Route::get('get-supervisor-comercial', [SupervisorComercialController::class, 'create'])->name('get-supervisor-comercial.create');
        Route::get('get-table-supervisor-usuario', [SupervisorComercialController::class, 'list'])->name('get-table-supervisor-usuario');
        Route::get('edit-supervisor-usuario', [SupervisorComercialController::class, 'update'])->name('edit-supervisor-usuario');
        Route::delete('supervisor-usuario-delete', [SupervisorComercialController::class, 'destroy'])->name('supervisor-usuario.delete');


        Route::get('gerente-unidade', [GerenteUnidadeController::class, 'index'])->name('gerente-unidade.index');
        Route::post('create-gerente-unidade', [GerenteUnidadeController::class, 'create'])->name('gerente-unidade.create');
        Route::get('get-table-gerente-usuario', [GerenteUnidadeController::class, 'list'])->name('get-table-gerente-usuario');
        Route::get('edit-gerente-unidade', [GerenteUnidadeController::class, 'update'])->name('edit-gerente-unidade');
        Route::delete('gerente-unidade-delete', [GerenteUnidadeController::class, 'destroy'])->name('gerente-unidade.delete');

        Route::get('vendedor-comercial', [VendedorController::class, 'index'])->name('vendedor-comercial.index');
        Route::post('create-vendedor-comercial', [VendedorController::class, 'create'])->name('vendedor-comercial.create');
        Route::get('get-table-vendedor-usuario', [VendedorController::class, 'list'])->name('get-table-vendedor-usuario');
        Route::post('edit-vendedor-comercial', [VendedorController::class, 'update'])->name('edit-vendedor-comercial');
        Route::delete('vendedor-comercial-delete', [VendedorController::class, 'destroy'])->name('vendedor-comercial.delete');
    });
});

Route::middleware(['permission:ver-pedidos-coletados-acompanhamento'])->group(function () {
    // Bloqueio de Pedidos
    Route::get('movimento/acompanha-pedidos', [BloqueioPedidosController::class, 'index'])->name('bloqueio-pedidos');
    Route::get('movimento/get-bloqueio-pedidos', [BloqueioPedidosController::class, 'getBloqueioPedido'])->name('get-bloqueio-pedidos');
    Route::get('movimento/get-pedidos', [BloqueioPedidosController::class, 'getPedidoAcompanhar'])->name('get-pedido-acompanhar');
    Route::get('movimento/get-item-pedidos', [BloqueioPedidosController::class, 'getItemPedidoAcompanhar'])->name('get-item-pedido-acompanhar');
    Route::get('movimento/get-detalhe-item-pedidos/{id}', [BloqueioPedidosController::class, 'getDetalheItemPedidoAcompanhar'])->name('get-detalhe-item-pedido');
});

Route::middleware(['permission:ver-analise-garantia'])->group(function () {
    Route::prefix('comercial')->group(function () {
        Route::get('analise-garantia', [GarantiaController::class, 'index'])->name('analise-garantia.index');
        Route::get('get-analise-garantia', [GarantiaController::class, 'getAnaliseGarantia'])->name('get-analise-garantia');
    });
});

Route::middleware(['permission:ver-coleta-empresa'])->group(function () {
    Route::prefix('coleta')->group(function () {
        Route::get('coleta-empresa-regiao-geral', [BloqueioPedidosController::class, 'coletaGeral'])->name('coleta-empresa-geral');
        Route::get('coleta-empresa-geral', [BloqueioPedidosController::class, 'coletaGeral'])->name('coleta-empresa-geral');
        Route::get('get-empresa-geral-regiao', [BloqueioPedidosController::class, 'getColetaGeralRegiao'])->name('get-coleta-empresa-geral-regiao');
        Route::get('get-empresa-geral', [BloqueioPedidosController::class, 'getColetaGeral'])->name('get-coleta-empresa-geral');
        Route::get('get-qtd-coleta', [BloqueioPedidosController::class, 'getQtdColeta'])->name('get-qtd-coleta');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('vendedor')->group(function () {
        Route::get('get-search-vendedor', [VendedorController::class, 'searchVendedor'])->name('get-search-vendedor');

        Route::get('comissao-vendedor-faturamento', [ComissaoController::class, 'comissaoVendedorFaturamento'])->name('comissao-vendedor-faturamento');
        Route::get('get-comissao-vendedor-faturamento', [ComissaoController::class, 'getComissaoVendedorFaturamento'])->name('get-comissao-vendedor-faturamento');
    });
});


Route::middleware(['auth'])->group(function () {
    Route::prefix('coleta')->group(function () {
        Route::get('coleta', [ColetaController::class, 'coleta'])->name('coleta');
        Route::get('get-coleta-geral', [ColetaController::class, 'getColetaGeral'])->name('get-coleta-geral');

        Route::get('coleta-medidas', [ColetaController::class, 'coletaMedidas'])->name('coleta-medidas');
        Route::get('get-coleta-medidas', [ColetaController::class, 'getColeta'])->name('get-coleta-medidas');

        Route::get('coleta-vendedor', [ColetaController::class, 'coletaVendedor'])->name('coleta-vendedor');
        Route::get('get-coleta-vendedor-mes', [ColetaController::class, 'getColetaVendedorMes'])->name('get-coleta-vendedor-mes');

        Route::get('vendedor', [ColetaController::class, 'vendedor'])->name('vendedor');
        Route::get('get-vendedor', [ColetaController::class, 'getVendedor'])->name('get-vendedor-acompanhamento');

        Route::get('coleta-producao', [ColetaController::class, 'coletaProducao'])->name('coleta-producao');
        Route::get('fichas-abertas', [ColetaController::class, 'fichasAbertas'])->name('fichas-abertas');
        Route::get('inadimplencia-vendedor', [ColetaController::class, 'inadimplenciaVendedor'])->name('inadimplencia-vendedor');
        Route::get('resumo-carga', [ColetaController::class, 'resumoCarga'])->name('resumo-carga');
        Route::get('producao-indicadores', [ColetaController::class, 'producaoIndicadores'])->name('producao-indicadores');
        Route::get('producao-carga', [ColetaController::class, 'producaoCarga'])->name('producao-carga');


        Route::get('pneus-etapas-executar', [ColetaController::class, 'pneusEtapasExecutar'])->name('pneus-etapas-executar');
        Route::get('pneus-parados-quatro-horas', [ColetaController::class, 'pneusParados'])->name('pneus-parados-quatro-horas');
        Route::get('exame-inicial-cobertura', [ColetaController::class, 'exameInicialCobertura'])->name('exame-inicial-cobertura');
        Route::get('exame-inicial', [ColetaController::class, 'exameInicial'])->name('exame-inicial');
        Route::get('retrabalho', [ColetaController::class, 'retrabalho'])->name('retrabalho');
        Route::get('inventario', [ColetaController::class, 'inventario'])->name('inventario');
    });
});


Route::middleware(['auth'])->group(function () {
    Route::prefix('borracharia')->group(function () {
        Route::get('requisicao-borracharia', [VendedorBorrachariaController::class, 'index'])->name('requisicao-borracharia.index');
        Route::get('get-requisicao-borracharia', [VendedorBorrachariaController::class, 'getRequisicaoBorracharia'])->name('get-requisicao-borracharia');
        Route::get('get-detalhe-requisicao-borracharia', [VendedorBorrachariaController::class, 'getDetailsRequisicaoBorracharia'])->name('get-detalhes-requisicao-borracharia');
        Route::post('desabilita-cliente-borracharia', [VendedorBorrachariaController::class, 'desabilitaClienteBorracharia'])->name('desabilita-cliente-borracharia');
      
    });
});
