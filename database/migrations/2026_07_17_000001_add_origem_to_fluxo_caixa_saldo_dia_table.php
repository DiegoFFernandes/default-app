<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // O cache guarda o "Saldo do Dia" já calculado, que depende da origem do Saldo Banco
        // ('digitado' = fluxo_caixa_saldo, 'firebird' = SALDOCAIXA). Sem essa coluna o cache de
        // uma origem ancorava a semana da outra ao trocar o toggle, mostrando número errado.
        //
        // As linhas existentes não têm como saber de qual origem vieram (o toggle já foi
        // alternado desde que foram gravadas), então são descartadas — o cache se repopula
        // sozinho conforme o usuário navega pelos períodos.
        DB::table('fluxo_caixa_saldo_dia')->truncate();

        Schema::table('fluxo_caixa_saldo_dia', function (Blueprint $table) {
            $table->dropUnique('fluxo_caixa_saldo_dia_dt_saldo_unique');
            $table->string('origem', 10)->default('digitado')->after('dt_saldo');
            $table->unique(['dt_saldo', 'origem'], 'fluxo_caixa_saldo_dia_dt_saldo_origem_unique');
        });
    }

    public function down(): void
    {
        // Sem a coluna origem não dá pra manter as duas origens na mesma data (o unique volta a
        // ser só por dt_saldo) — descarta tudo em vez de escolher arbitrariamente qual mantém.
        DB::table('fluxo_caixa_saldo_dia')->truncate();

        Schema::table('fluxo_caixa_saldo_dia', function (Blueprint $table) {
            $table->dropUnique('fluxo_caixa_saldo_dia_dt_saldo_origem_unique');
            $table->dropColumn('origem');
            $table->unique('dt_saldo', 'fluxo_caixa_saldo_dia_dt_saldo_unique');
        });
    }
};
