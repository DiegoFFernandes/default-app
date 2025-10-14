<?php
use App\Http\Controllers\Admin\TarefasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('tarefas')->group(function () {
        Route::get('quadro-tarefas', [TarefasController::class, 'tarefas'] )->name('tarefas-quadro');
        Route::get('listar-tarefas', [TarefasController::class, 'listarTarefas'] )->name('listar-tarefas');
    });
});