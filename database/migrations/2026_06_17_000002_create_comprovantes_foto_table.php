<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('comprovantes_foto')) {
            return;
        }

        Schema::create('comprovantes_foto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_comprovante');
            $table->string('path');
            $table->timestamps();

            $table->foreign('id_comprovante')->references('id')->on('comprovantes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprovantes_foto');
    }
};
