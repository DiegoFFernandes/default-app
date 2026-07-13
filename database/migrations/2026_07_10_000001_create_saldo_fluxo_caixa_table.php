<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldo_fluxo_caixa', function (Blueprint $table) {
            $table->id();
            $table->string('ds_banco', 100);
            $table->decimal('vl_saldo', 12, 2);
            $table->date('dt_saldo');
            $table->unsignedBigInteger('id_user');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_fluxo_caixa');
    }
};
