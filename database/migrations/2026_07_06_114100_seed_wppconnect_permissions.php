<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'ver-wppconnect',       'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'wppconnect-configurar', 'guard_name' => 'web']);
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('guard_name', 'web')
            ->whereIn('name', ['ver-wppconnect', 'wppconnect-configurar'])
            ->delete();
    }
};
