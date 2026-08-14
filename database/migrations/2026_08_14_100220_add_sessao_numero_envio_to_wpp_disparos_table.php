<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('wpp_disparos', function (Blueprint $table) {
            $table->string('sessao', 50)->nullable()->after('phone')->comment('session_name da conexão usada no envio');
            $table->string('numero_envio', 20)->nullable()->after('sessao')->comment('Número que enviou (wpp_sessoes.numero no momento do envio)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wpp_disparos', function (Blueprint $table) {
            $table->dropColumn(['sessao', 'numero_envio']);
        });
    }
};
