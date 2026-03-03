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
        Schema::create('lote_estoque', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cd_empresa');
            $table->string('descricao', 30);
            $table->bigInteger('cd_ordemcompra')->nullable();
            $table->bigInteger('qtd_itens')->nullable();
            $table->float('ps_liquido_total', 8, 2)->nullable();
            $table->char('status', 1);
            $table->char('tp_lote', 1);
            $table->unsignedBigInteger('tp_produto');
            $table->unsignedBigInteger('id_marca_lote');
            $table->unsignedBigInteger('cd_usuario');
            $table->timestamps();

            $table->foreign('cd_usuario')->references('id')->on('users');
            $table->foreign('id_marca_lote')->references('id')->on('marca_lote_estoque');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lote_estoque');
    }
};
