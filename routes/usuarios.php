<?php

use App\Http\Controllers\Admin\ConfigurationUsersController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PermissionUserController;
use App\Http\Controllers\admin\PessoaController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('usuario')->group(function () {        
        Route::post('search-pessoa', [UserController::class, 'searchPessoa'])->name('usuario.search-pessoa');        
    });
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('usuario')->group(function () {
        Route::get('index', [UserController::class, 'index'])->name('usuario.index');
        Route::get('listar-usuarios', [UserController::class, 'listUser'])->name('usuario.list');        
        Route::post('cadastrar', [UserController::class, 'create'])->name('usuario.create.do');
        Route::delete('delete', [UserController::class, 'destroy'])->name('usuario.delete');
        Route::post('desative', [UserController::class, 'desative'])->name('usuario.desative');
        Route::post('atualizar', [UserController::class, 'update'])->name('usuario.update');

        /*Rotas funções*/
        Route::get('funcao-user', [RoleController::class, 'index'])->name('usuario.role');
        Route::get('list-funcao', [RoleController::class, 'listUserRole'])->name('usuario.list_role');
        Route::get('funcao/editar/{id}', [RoleController::class, 'edit'])->name('usuario.role.edit');
        Route::post('funcao/editar', [RoleController::class, 'update'])->name('usuario.role.edit.do');
        Route::get('funcao/novo', [RoleController::class, 'create'])->name('usuario.role.create');
        Route::post('funcao/novo', [RoleController::class, 'save'])->name('usuario.role.create.do');
        Route::get('funcao/delete/{id}', [RoleController::class, 'delete'])->name('usuario.role.delete');
        Route::get('funcao/get-users', [RoleController::class, 'getUsers'])->name('usuario.role.get-users');
        Route::get('funcao/get-roles', [RoleController::class, 'getRoles'])->name('usuario.role.get-roles');
        Route::post('funcao/atribuir', [RoleController::class, 'assign'])->name('usuario.role.assign');
        Route::post('funcao/atualizar', [RoleController::class, 'updateRole'])->name('usuario.role.update');
        Route::delete('funcao/remover', [RoleController::class, 'removeUser'])->name('usuario.role.remove');

        /*Rotas permission-role*/
        Route::prefix('permissao-role')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('usuario.permission-role');
            Route::get('list', [PermissionController::class, 'listRolePermission'])->name('usuario.permission-role.list');
            Route::get('get-roles', [PermissionController::class, 'getRoles'])->name('usuario.permission-role.get-roles');
            Route::get('get-permissions', [PermissionController::class, 'getPermissions'])->name('usuario.permission-role.get-permissions');
            Route::post('atribuir', [PermissionController::class, 'assign'])->name('usuario.permission-role.assign');
            Route::post('atualizar', [PermissionController::class, 'updatePermission'])->name('usuario.permission-role.update');
            Route::delete('remover', [PermissionController::class, 'removePermissions'])->name('usuario.permission-role.remove');
        });

        /*Rotas permission-user*/
        Route::prefix('permissao-user')->group(function () {
            Route::get('/', [PermissionUserController::class, 'index'])->name('usuario.permission-user');
            Route::get('list', [PermissionUserController::class, 'listUserPermission'])->name('usuario.permission-user.list');
            Route::get('get-users', [PermissionUserController::class, 'getUsers'])->name('usuario.permission-user.get-users');
            Route::get('get-permissions', [PermissionUserController::class, 'getPermissions'])->name('usuario.permission-user.get-permissions');
            Route::post('atribuir', [PermissionUserController::class, 'assign'])->name('usuario.permission-user.assign');
            Route::post('atualizar', [PermissionUserController::class, 'updatePermission'])->name('usuario.permission-user.update');
            Route::delete('remover', [PermissionUserController::class, 'removePermissions'])->name('usuario.permission-user.remove');
        });

        // Configurações de Usuários
        Route::get('configuration-users', [ConfigurationUsersController::class, 'index'])->name('usuario.configuration-users');
    });
});

Route::middleware(['auth', 'permission:ver-cadastros'])->group(function () {
    Route::prefix('cadastro')->group(function () {

        Route::get('pessoa', [PessoaController::class, 'index'])->name('pessoa.index');
        Route::get('get-pessoa', [PessoaController::class, 'create'])->name('get-pessoa.create');
        Route::get('get-table-pessoa-usuario', [PessoaController::class, 'list'])->name('get-table-pessoa-usuario');
        Route::post('edit-pessoa-usuario', [PessoaController::class, 'update'])->name('edit-pessoa-usuario');
        Route::delete('pessoa-usuario-delete', [PessoaController::class, 'destroy'])->name('pessoa-usuario.delete');
    });
});
