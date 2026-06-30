<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->dropUnique('cobranca_parametros_chave_unique');
        });
    }

    public function down(): void
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->unique('chave', 'cobranca_parametros_chave_unique');
        });
    }
};
