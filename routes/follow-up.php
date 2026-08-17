<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FollowUpController;
use App\Http\Controllers\Admin\DisparoAutomaticoController;

Route::middleware(['auth', 'permission:ver-follow-up'])->group(function () {
    Route::prefix('follow-up')->group(function () {
        Route::get('envio-follow-up', [FollowUpController::class, 'searchEnvio'])->name('search-envio');
        Route::get('get-search-follow', [FollowUpController::class, 'getSearchEnvio'])->name('get-search-envio');
        Route::get('get-email-follow', [FollowUpController::class, 'getEmailEnvio'])->name('get-email-follow');
        Route::post('reenvia-follow', [FollowUpController::class, 'reenviaFollow'])->name('reenvia-follow');
    });

    Route::prefix('disparo-automatico')->group(function () {
        Route::get('contextos',                  [DisparoAutomaticoController::class, 'listContextos'])->name('disparo-automatico.contextos');
        Route::post('contextos/{id}/toggle',      [DisparoAutomaticoController::class, 'toggleContexto'])->name('disparo-automatico.contextos.toggle');
        Route::post('contextos/{id}/horario',     [DisparoAutomaticoController::class, 'updateHorarioContexto'])->name('disparo-automatico.contextos.horario');
        Route::post('contextos/{id}/whatsapp',    [DisparoAutomaticoController::class, 'updateWhatsAppContexto'])->name('disparo-automatico.contextos.whatsapp');
        Route::get('envios',                      [DisparoAutomaticoController::class, 'listEnvios'])->name('disparo-automatico.envios');
        Route::post('envios/pendente',            [DisparoAutomaticoController::class, 'criarEnvioPendente'])->name('disparo-automatico.envios.criar-pendente');
        Route::post('envios/{id}/reenviar',       [DisparoAutomaticoController::class, 'reenviarEnvio'])->name('disparo-automatico.envios.reenviar');
        Route::post('envios/{id}/email',          [DisparoAutomaticoController::class, 'atualizarEmailEnvio'])->name('disparo-automatico.envios.email');
        Route::post('envios/{id}/telefone',       [DisparoAutomaticoController::class, 'atualizarTelefoneEnvio'])->name('disparo-automatico.envios.telefone');
        Route::get('envios/{id}/preview',                 [DisparoAutomaticoController::class, 'previewEnvio'])->name('disparo-automatico.envios.preview');
        Route::get('envios/{id}/preview/anexo/{indice}',       [DisparoAutomaticoController::class, 'previewAnexo'])->name('disparo-automatico.envios.preview.anexo');
        Route::get('envios/{id}/preview/anexo/{indice}/html',  [DisparoAutomaticoController::class, 'previewAnexoHtml'])->name('disparo-automatico.envios.preview.anexo.html');
    });
});
