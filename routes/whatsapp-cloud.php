<?php

use App\Http\Controllers\Admin\WhatsappCloudWebhookController;
use App\Http\Controllers\Admin\WhatsappMensagemController;
use App\Http\Controllers\Admin\WhatsappTemplateController;
use Illuminate\Support\Facades\Route;

// Webhook da API Oficial (WhatsApp Cloud API) — sem autenticação
Route::get('whatsapp-cloud/webhook', [WhatsappCloudWebhookController::class, 'verify'])
    ->name('whatsapp-cloud.webhook.verify');

Route::post('whatsapp-cloud/webhook', [WhatsappCloudWebhookController::class, 'handle'])
    ->name('whatsapp-cloud.webhook.handle');

// Gerenciamento de templates (WABA) — tela em admin/whatsapp/oficial
Route::middleware(['auth', 'permission:whatsapp-oficial-configurar'])->prefix('whatsapp-oficial')->group(function () {
    Route::get('/',                 [WhatsappTemplateController::class, 'index'])->name('whatsapp-oficial.index');
    Route::post('/',                [WhatsappTemplateController::class, 'store'])->name('whatsapp-oficial.store');
    Route::put('{template}',        [WhatsappTemplateController::class, 'update'])->name('whatsapp-oficial.update');
    Route::post('sincronizar',      [WhatsappTemplateController::class, 'sincronizar'])->name('whatsapp-oficial.sincronizar');
    Route::post('{template}/enviar',[WhatsappTemplateController::class, 'submeter'])->name('whatsapp-oficial.enviar');
    Route::delete('{template}',     [WhatsappTemplateController::class, 'destroy'])->name('whatsapp-oficial.destroy');

    Route::get('mensagens',         [WhatsappMensagemController::class, 'index'])->name('whatsapp-oficial.mensagens.index');
    Route::post('mensagens',        [WhatsappMensagemController::class, 'store'])->name('whatsapp-oficial.mensagens.store');
});
