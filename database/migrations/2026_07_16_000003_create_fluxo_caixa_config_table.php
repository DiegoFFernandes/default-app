<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fluxo_caixa_config', function (Blueprint $table) {
            $table->string('chave')->primary();
            $table->text('valor')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // Padrão 'digitado' preserva o comportamento atual (fluxo_caixa_saldo) — o usuário troca
        // pra 'firebird' (SALDOCAIXA) só quando a tela de configuração estiver pronta.
        DB::table('fluxo_caixa_config')->insert([
            ['chave' => 'origem_saldo_banco', 'valor' => 'digitado'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fluxo_caixa_config');
    }
};
