<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('comprovante') || !Schema::hasTable('comprovantes')) {
            return;
        }

        Schema::rename('comprovantes', 'comprovante');
    }

    public function down(): void
    {
        if (Schema::hasTable('comprovantes') || !Schema::hasTable('comprovante')) {
            return;
        }

        Schema::rename('comprovante', 'comprovantes');
    }
};
