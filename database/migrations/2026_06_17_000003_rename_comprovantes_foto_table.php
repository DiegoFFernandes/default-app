<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('comprovante_foto') || !Schema::hasTable('comprovantes_foto')) {
            return;
        }

        Schema::rename('comprovantes_foto', 'comprovante_foto');
    }

    public function down(): void
    {
        if (Schema::hasTable('comprovantes_foto') || !Schema::hasTable('comprovante_foto')) {
            return;
        }

        Schema::rename('comprovante_foto', 'comprovantes_foto');
    }
};
