<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Renomeia a permission existente preservando os assignments de roles/users
        Permission::where('name', 'ver-estoque')->where('guard_name', 'web')
            ->update(['name' => 'ver-estoque-carcacas']);

        Permission::firstOrCreate(['name' => 'ver-estoque-negativo',  'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'ver-contagem-estoque',  'guard_name' => 'web']);
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'ver-estoque-carcacas')->where('guard_name', 'web')
            ->update(['name' => 'ver-estoque']);

        Permission::where('guard_name', 'web')
            ->whereIn('name', ['ver-estoque-negativo', 'ver-contagem-estoque'])
            ->delete();
    }
};
