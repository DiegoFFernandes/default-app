<?php

use App\Http\Controllers\admin\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::prefix('usuario')->group(function () {
        Route::get('index', [UserController::class, 'index'])->name('usuario.index');
        Route::get('listar-usuarios', [UserController::class, 'listUser'])->name('usuario.list');
        Route::get('search-pessoa', [UserController::class, 'searchPessoa'])->name('usuario.search-pessoa');
        Route::post('cadastrar', [UserController::class, 'create'])->name('usuario.create.do');
        Route::delete('delete', [UserController::class, 'destroy'])->name('usuario.delete');
    });
});