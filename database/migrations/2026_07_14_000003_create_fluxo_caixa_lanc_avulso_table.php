<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fluxo_caixa_lanc_avulso', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 10); // 'receber' ou 'pagar'
            $table->date('dt_lancamento');
            $table->unsignedInteger('cd_pessoa')->nullable();
            $table->string('nm_pessoa', 150)->nullable();
            $table->decimal('vl_documento', 12, 2);
            $table->unsignedInteger('cd_tipoconta');
            $table->string('ds_tipoconta', 100)->nullable();
            $table->string('cd_formapagto', 10)->nullable();
            $table->string('ds_formapagto', 100)->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fluxo_caixa_lanc_avulso');
    }
};
