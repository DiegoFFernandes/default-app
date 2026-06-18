<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('comprovantes_foto', 'comprovante_foto');
    }

    public function down(): void
    {
        Schema::rename('comprovante_foto', 'comprovantes_foto');
    }
};
