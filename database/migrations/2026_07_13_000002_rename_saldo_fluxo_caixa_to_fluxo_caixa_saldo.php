<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('saldo_fluxo_caixa', 'fluxo_caixa_saldo');
    }

    public function down(): void
    {
        Schema::rename('fluxo_caixa_saldo', 'saldo_fluxo_caixa');
    }
};
