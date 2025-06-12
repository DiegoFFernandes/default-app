<?php

use App\Http\Controllers\Admin\AreaComercialController;
use App\Http\Controllers\Admin\BloqueioPedidosController;
use App\Http\Controllers\admin\GarantiaController;
use App\Http\Controllers\Admin\LiberaOrdemComissaoController;
use App\Http\Controllers\Admin\ProducaoController;
use App\Http\Controllers\Admin\RegiaoComercialController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:admin|gerencia'])->group(function () {
    Route::prefix('libera-ordem-comercial')->group(function () {
        Route::get('index', [LiberaOrdemComissaoController::class, 'index'])->name('libera-ordem-comissao.index');

        Route::get('get-ordem-bloqueadas-comercial', [LiberaOrdemComissaoController::class, 'getListOrdemBloqueadas'])->name('get-ordens-bloqueadas-comercial');
        Route::get('get-pneus-ordem-bloqueadas-comercial/{id}', [LiberaOrdemComissaoController::class, 'getListPneusOrdemBloqueadas'])->name('get-pneus-ordens-bloqueadas-comercial');
        Route::post('save-libera-pedido', [LiberaOrdemComissaoController::class, 'saveLiberaPedido'])->name('save-libera-pedido');
    });
});

Route::middleware(['auth', 'role:admin|gerencia'])->group(function () {
    Route::prefix('producao')->group(function () {
        Route::get('pneus-produzidos-sem-faturar', [ProducaoController::class, 'index'])->name('produzidos-sem-faturar');
        Route::get('get-pneus-produzidos-sem-faturar', [ProducaoController::class, 'getListPneusProduzidosFaturar'])->name('get-pneus-produzidos-sem-faturar');
        Route::get('get-pneus-produzidos-sem-faturar-details', [ProducaoController::class, 'getListPneusProduzidosFaturarDetails'])->name('get-pneus-produzidos-sem-faturar-details');
    });
});


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('cadastro')->group(function () {

        Route::get('area-comercial', [AreaComercialController::class, 'index'])->name('area-comercial.index');
        Route::get('get-area-comercial', [AreaComercialController::class, 'create'])->name('get-area-comercial.create');
        Route::get('get-table-area-usuario', [AreaComercialController::class, 'list'])->name('get-table-area-usuario');
        Route::post('edit-area-usuario', [AreaComercialController::class, 'update'])->name('edit-area-usuario');
        Route::delete('area-usuario-delete', [AreaComercialController::class, 'destroy'])->name('area-usuario.delete');

        Route::get('regiao-comercial', [RegiaoComercialController::class, 'index'])->name('regiao-comercial.index');
        Route::get('get-regiao-comercial', [RegiaoComercialController::class, 'create'])->name('get-regiao-comercial.create');
        Route::get('get-table-regiao-usuario', [RegiaoComercialController::class, 'list'])->name('get-table-regiao-usuario');
        Route::post('edit-regiao-usuario', [RegiaoComercialController::class, 'update'])->name('edit-regiao-usuario');
        Route::delete('regiao-usuario-delete', [RegiaoComercialController::class, 'destroy'])->name('regiao-usuario.delete');
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

Route::middleware(['role:admin'])->group(function () {
    Route::prefix('coleta')->group(function () {
        Route::get('coleta-empresa-geral', [BloqueioPedidosController::class, 'coletaGeral'])->name('coleta-empresa-geral');
        Route::get('get-empresa-geral', [BloqueioPedidosController::class, 'getColetaGeral'])->name('get-coleta-empresa-geral');
    });
});
