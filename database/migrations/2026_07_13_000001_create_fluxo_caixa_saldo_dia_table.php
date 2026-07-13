<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fluxo_caixa_saldo_dia', function (Blueprint $table) {
            $table->id();
            $table->date('dt_saldo')->unique();
            $table->decimal('vl_saldo', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fluxo_caixa_saldo_dia');
    }
};
