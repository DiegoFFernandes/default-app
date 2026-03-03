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
        Schema::create('item', function (Blueprint $table) {
            $table->id();
            $table->string('cd_codbarraemb', 100)->nullable()->unique();
            $table->unsignedBigInteger('cd_item')->unique();
            $table->string('ds_item', 100);
            $table->float('ps_liquido', 8, 2)->nullable();
            $table->string('sg_unidmed', 2);
            $table->unsignedBigInteger('cd_subgrupo');
            $table->unsignedBigInteger('cd_marca');
            $table->unsignedBigInteger('cd_usuario');
            $table->string('st_ativo', 1);
            $table->timestamps();

            $table->foreign('cd_usuario')->references('id')->on('users');
            $table->foreign('cd_marca')->references('id')->on('marca_pneus');
            $table->foreign('cd_subgrupo')->references('id')->on('sub_grupos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item');
    }
};
