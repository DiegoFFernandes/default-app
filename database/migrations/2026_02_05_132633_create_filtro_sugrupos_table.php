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
        Schema::create('filtro_sugrupos', function (Blueprint $table) {
            $table->id();
            $table->integer('cd_sugrupo');
            $table->string('ds_sugrupo', 255);
            $table->foreignId('cd_agrupamento');    
            $table->char('st_ativo', 1)->default('S');       
            $table->timestamps();

            $table->foreign('cd_agrupamento')->references('id')->on('filtro_agrupamentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filtro_sugrupos');
    }
};
