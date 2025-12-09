<?php

use App\Http\Controllers\Fcm\FCMController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('fcm')->group(function () {
        Route::post('device-token', [FCMController::class, 'saveToken'])->name('device-token');        
    });   

});

Route::get('notify-user', [FCMController::class, 'sendToUser'])->name('notify-user');
