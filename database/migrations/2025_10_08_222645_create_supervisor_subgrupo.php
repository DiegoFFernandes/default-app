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
        Schema::create('supervisor_subgrupo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cd_user_supervisor');
            $table->bigInteger('cd_subgrupo');

            $table->foreign('cd_user_supervisor')->references('cd_usuario')->on('supervisor_comercial');
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
        Schema::dropIfExists('supervisor_subgrupo');
    }
};
