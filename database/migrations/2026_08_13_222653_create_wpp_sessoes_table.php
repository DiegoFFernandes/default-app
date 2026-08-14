<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wpp_sessoes', function (Blueprint $table) {
            $table->id();
            $table->string('setor', 30)->unique();        // chave do setor (ex: compras)
            $table->string('session_name', 50)->unique(); // nome da sessão no wppconnect-server
            $table->timestamps();
        });

        // Toda instalação nasce com a conexão "Geral": é ela que atende os módulos
        // sem sessão própria, por isso não pode ser excluída pela tela. As demais
        // o usuário cadastra conforme a necessidade.
        // Literal de propósito — migration não deve depender de config/.env, senão
        // o resultado muda conforme o ambiente em que roda.
        $agora = now();

        foreach (['geral' => 'geral'] as $setor => $sessionName) {
            DB::table('wpp_sessoes')->insert([
                'setor'        => $setor,
                'session_name' => $sessionName,
                'created_at'   => $agora,
                'updated_at'   => $agora,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wpp_sessoes');
    }
};
