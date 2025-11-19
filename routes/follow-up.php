<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FollowUpController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('follow-up')->group(function () {
        Route::get('envio-follow-up', [FollowUpController::class, 'searchEnvio'])->name('search-envio');
        Route::get('get-search-follow', [FollowUpController::class, 'getSearchEnvio'])->name('get-search-envio');
        Route::get('get-email-follow', [FollowUpController::class, 'getEmailEnvio'])->name('get-email-follow');
        Route::post('reenvia-follow', [FollowUpController::class, 'reenviaFollow'])->name('reenvia-follow');
    });
});
