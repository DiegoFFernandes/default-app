<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\admin\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::prefix('usuario')->group(function () {
        Route::get('index', [UserController::class, 'index'])->name('usuario.index');
        Route::get('listar-usuarios', [UserController::class, 'listUser'])->name('usuario.list');
        Route::get('search-pessoa', [UserController::class, 'searchPessoa'])->name('usuario.search-pessoa');
        Route::post('cadastrar', [UserController::class, 'create'])->name('usuario.create.do');
        Route::delete('delete', [UserController::class, 'destroy'])->name('usuario.delete');
        Route::post('atualizar', [UserController::class, 'update'])->name('usuario.update');

        /*Rotas funções*/
        Route::get('funcao', [RoleController::class, 'index'])->name('usuario.role');
        Route::get('list-funcao', [RoleController::class, 'listUserRole'])->name('usuario.list_role');
        Route::get('funcao/editar/{id}', [RoleController::class, 'edit'])->name('usuario.role.edit');
        Route::post('funcao/editar', [RoleController::class, 'update'])->name('usuario.role.edit.do');
        Route::get('funcao/novo', [RoleController::class, 'create'])->name('usuario.role.create');
        Route::post('funcao/novo', [RoleController::class, 'save'])->name('usuario.role.create.do');
        Route::get('funcao/delete/{id}', [RoleController::class, 'delete'])->name('usuario.role.delete');

        /*Rotas permission*/
        Route::get('permissao', [PermissionController::class, 'index'])->name('usuario.permission');
        Route::get('permissao/editar/{id}', [PermissionController::class, 'edit'])->name('usuario.permission.edit');
        Route::post('permissao/editar', [PermissionController::class, 'update'])->name('usuario.permission.edit.do');
        Route::get('permissao/novo', [PermissionController::class, 'create'])->name('usuario.permission.create');
        Route::post('permissao/novo', [PermissionController::class, 'save'])->name('usuario.permission.create.do');
        Route::get('permissao/delete/{id}', [PermissionController::class, 'delete'])->name('usuario.permission.delete');
    });
});
