<?php
use App\Http\Controllers\Admin\TarefasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('tarefas')->group(function () {
        Route::get('quadro-tarefas', [TarefasController::class, 'tarefas'] )->name('tarefas-quadro');
        Route::get('listar-colunas', [TarefasController::class, 'listarColunas'] )->name('listar-colunas');
        Route::post('salvar-tarefas', [TarefasController::class, 'salvarTarefas'] )->name('salvar-tarefas');


        Route::get('listar-cartoes', [TarefasController::class, 'listarCartoes'])->name('listar-cartoes');
        Route::post('editar-cartoes', [TarefasController::class, 'editarCartoes'])->name('editar-cartoes');
        Route::get('deletar-cartao', [TarefasController::class, 'deletarCartao'])->name('deletar-cartao');

        Route::post('reordenar-cartao', [TarefasController::class, 'reordenarCartao'])->name('reordenar-cartao');
        Route::post('reordenar-colunas', [TarefasController::class, 'reordenarColunas'])->name('reordenar-colunas');


        Route::post('editar-coluna', [TarefasController::class, 'editarColuna'])->name('editar-coluna');
        Route::post('arquivar-coluna', [TarefasController::class, 'arquivarColuna'])->name('arquivar-coluna');
    });
});