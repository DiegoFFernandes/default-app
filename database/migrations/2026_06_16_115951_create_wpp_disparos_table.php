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
        Schema::create('wpp_disparos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->bigInteger('phone');
            $table->text('mensagem');
            $table->char('status', 1)->default('E')->comment('E=Enviado, F=Falha');
            $table->text('erro')->nullable();
            $table->timestamp('dt_envio')->nullable();
            $table->timestamp('dt_registro')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wpp_disparos');
    }
};
