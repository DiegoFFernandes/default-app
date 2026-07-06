<?php

use App\Http\Controllers\Admin\WppAcaoController;
use App\Http\Controllers\Admin\WppConnectController;
use App\Http\Controllers\Admin\WppWebhookController;
use Illuminate\Support\Facades\Route;

// Webhook — sem autenticação
Route::post('wppconnect/webhook', [WppWebhookController::class, 'handle'])
    ->name('wppconnect.webhook');

// Links de aprovação — requer login (Laravel redireciona para /login e volta após autenticar)
Route::middleware('auth')->group(function () {
    Route::get('compras/acao',  [WppAcaoController::class, 'show'])
        ->name('wppconnect.acao.show');

    Route::post('compras/acao', [WppAcaoController::class, 'processar'])
        ->name('wppconnect.acao.processar');
});

// Visualização e operação do WhatsApp
Route::middleware(['auth', 'permission:ver-wppconnect'])->group(function () {
    Route::prefix('wppconnect')->group(function () {
        Route::get('/',                       [WppConnectController::class, 'index'])->name('wppconnect.index');
        Route::post('close-session',          [WppConnectController::class, 'closeSession'])->name('wppconnect.close-session');
        Route::post('logout-session',         [WppConnectController::class, 'logoutSession'])->name('wppconnect.logout-session');
        Route::post('start-session',          [WppConnectController::class, 'startSession'])->name('wppconnect.start-session');
        Route::post('start-session-phone',    [WppConnectController::class, 'startSessionPhone'])->name('wppconnect.start-session-phone');
        Route::get('phone-code',              [WppConnectController::class, 'phoneCode'])->name('wppconnect.phone-code');
        Route::get('status',                  [WppConnectController::class, 'status'])->name('wppconnect.status');
        Route::get('qrcode',                  [WppConnectController::class, 'qrCode'])->name('wppconnect.qrcode');
        Route::get('disparos',                [WppConnectController::class, 'disparos'])->name('wppconnect.disparos');
        Route::post('disparos/{id}/reenviar', [WppConnectController::class, 'reenviar'])->name('wppconnect.disparos.reenviar');
    });
});

// Configuração de parâmetros e permissões
Route::middleware(['auth', 'permission:wppconnect-configurar'])->group(function () {
    Route::prefix('wppconnect')->group(function () {
        Route::get('parametros',              [WppConnectController::class, 'parametros'])->name('wppconnect.parametros');
        Route::post('parametros/{chave}',     [WppConnectController::class, 'salvarParametro'])->name('wppconnect.parametros.salvar');
        Route::post('usuarios/{id}/wppconnect-ia', [WppConnectController::class, 'toggleWppIa'])->name('wppconnect.usuarios.wppconnect-ia');
        Route::post('usuarios/{id}/wpp-lid',   [WppConnectController::class, 'salvarWppLid'])->name('wppconnect.usuarios.wpp-lid');
        Route::get('lids-pendentes',           [WppConnectController::class, 'lidsPendentes'])->name('wppconnect.lids-pendentes');
        Route::post('lids-pendentes/associar', [WppConnectController::class, 'associarLid'])->name('wppconnect.lids-pendentes.associar');
    });
});
