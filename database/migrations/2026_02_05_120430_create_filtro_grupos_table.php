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
        Schema::create('filtro_grupos', function (Blueprint $table) {
            $table->id();
            $table->integer('cd_grupo');
            $table->string('ds_grupo', 255);
            $table->char('st_ativo', 1)->default('S');
            $table->foreignId('cd_agrupamento');            

            $table->foreign('cd_agrupamento')->references('id')->on('filtro_agrupamentos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filtro_grupos');
    }
};
