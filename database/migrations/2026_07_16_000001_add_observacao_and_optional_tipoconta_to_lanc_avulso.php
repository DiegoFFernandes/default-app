<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fluxo_caixa_lanc_avulso', function (Blueprint $table) {
            $table->string('ds_observacao', 255)->nullable()->after('ds_formapagto');
        });

        // Pessoa/Tipo de Conta/Forma de Pagamento viraram opcionais — cd_pessoa e
        // cd_formapagto já eram nullable, só cd_tipoconta precisa mudar. MODIFY direto via SQL
        // porque não há doctrine/dbal instalado (exigido pelo Schema::table()->change()).
        DB::statement('ALTER TABLE fluxo_caixa_lanc_avulso MODIFY cd_tipoconta INT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE fluxo_caixa_lanc_avulso MODIFY cd_tipoconta INT UNSIGNED NOT NULL');

        Schema::table('fluxo_caixa_lanc_avulso', function (Blueprint $table) {
            $table->dropColumn('ds_observacao');
        });
    }
};
