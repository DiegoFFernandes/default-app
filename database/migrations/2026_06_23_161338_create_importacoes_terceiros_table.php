<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('importacoes_terceiros', function (Blueprint $table) {
            $table->id();
            $table->string('hash_arquivo', 64)->unique();
            $table->string('nm_arquivo', 255);
            $table->unsignedInteger('cd_empresa')->nullable();
            $table->foreignId('cd_usuario')->constrained('users');
            $table->unsignedInteger('total_registros')->default(0);
            $table->date('dt_referencia_inicio')->nullable();
            $table->date('dt_referencia_fim')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('importacoes_terceiros');
    }
};
