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
        Schema::create('item_lote_estoque', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cd_lote');
            $table->unsignedBigInteger('cd_item');
            $table->float('peso', 8, 2);
            $table->unsignedBigInteger('cd_usuario');
            $table->timestamps();

            $table->foreign('cd_lote')->references('id')->on('lote_estoque');
            $table->foreign('cd_item')
                ->references('cd_item')
                ->on('item')
                ->onDelete('cascade');

            $table->foreign('cd_usuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_lote_estoque');
    }
};
