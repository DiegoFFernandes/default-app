<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fluxo_caixa_compensacao', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cd_tipoconta')->unique();
            $table->string('ds_tipoconta', 100)->nullable();
            // Dias a somar no vencimento conforme o dia da semana em que ele cai.
            $table->unsignedTinyInteger('segunda')->default(0);
            $table->unsignedTinyInteger('terca')->default(0);
            $table->unsignedTinyInteger('quarta')->default(0);
            $table->unsignedTinyInteger('quinta')->default(0);
            $table->unsignedTinyInteger('sexta')->default(0);
            $table->unsignedTinyInteger('sabado')->default(0);
            $table->unsignedTinyInteger('domingo')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Seed mínimo com os 2 tipos de conta principais (1 = Contas a Pagar, 2 = Contas a
        // Receber), preservando o comportamento atual pra eles. Os demais CD_TIPOCONTA que já
        // existiam hardcoded em calcularDataPersonalizada() (12, 5, 17, 29, 31) não são mais
        // semeados aqui — o usuário cadastra pela tela de parâmetros quando precisar.
        $agora = now();

        DB::table('fluxo_caixa_compensacao')->insert([
            [
                'cd_tipoconta' => 2,
                'ds_tipoconta' => 'Contas a Receber',
                'segunda' => 1, 'terca' => 1, 'quarta' => 1, 'quinta' => 1,
                'sexta' => 3, 'sabado' => 3, 'domingo' => 2,
                'created_at' => $agora, 'updated_at' => $agora,
            ],
            [
                'cd_tipoconta' => 1,
                'ds_tipoconta' => 'Contas a Pagar',
                'segunda' => 1, 'terca' => 1, 'quarta' => 1, 'quinta' => 1,
                'sexta' => 3, 'sabado' => 2, 'domingo' => 1,
                'created_at' => $agora, 'updated_at' => $agora,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fluxo_caixa_compensacao');
    }
};
