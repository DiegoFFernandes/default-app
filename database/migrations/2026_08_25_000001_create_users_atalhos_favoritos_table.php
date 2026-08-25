<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_atalhos_favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->string('chave_atalho');
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();

            $table->unique(['id_usuario', 'chave_atalho']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_atalhos_favoritos');
    }
};
