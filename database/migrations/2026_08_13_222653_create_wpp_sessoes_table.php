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

        // Migra as sessões que hoje vivem no .env para o banco, para a tela
        // continuar exibindo as conexões já pareadas.
        $agora = now();

        foreach (config('services.wppconnect.sessions', []) as $setor => $sessionName) {
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
