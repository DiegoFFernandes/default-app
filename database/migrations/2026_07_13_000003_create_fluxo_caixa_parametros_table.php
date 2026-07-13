<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fluxo_caixa_parametros', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 10); // 'receber' ou 'pagar'
            $table->unsignedInteger('cd_tipoconta');
            $table->string('ds_tipoconta', 100)->nullable();
            $table->string('cd_formapagto', 10)->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Valores padrão: CD_TIPOCONTA 1 (Contas a Pagar) e 2 (Contas a Receber) são comuns a
        // qualquer base — os demais tipos (12, 5, 17, 29, 31 etc.) variam por empresa e ficam a
        // cargo do usuário cadastrar na tela de parâmetros.
        DB::table('fluxo_caixa_parametros')->insert([
            [
                'tipo' => 'pagar',
                'cd_tipoconta' => 1,
                'ds_tipoconta' => 'Contas a Pagar',
                'cd_formapagto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'receber',
                'cd_tipoconta' => 2,
                'ds_tipoconta' => 'Contas a Receber',
                'cd_formapagto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fluxo_caixa_parametros');
    }
};
