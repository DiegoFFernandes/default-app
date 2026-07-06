<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'wpp-ia')->where('guard_name', 'web')
            ->update(['name' => 'wppconnect-ia']);
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'wppconnect-ia')->where('guard_name', 'web')
            ->update(['name' => 'wpp-ia']);
    }
};
