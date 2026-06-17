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
            $table->string('token', 64)->nullable()->unique()->after('erro');
            $table->string('referencia_tipo', 50)->nullable()->after('token')->comment('Ex: compra_etapa');
            $table->unsignedBigInteger('referencia_id')->nullable()->after('referencia_tipo');
        });
    }

    public function down()
    {
        Schema::table('wpp_disparos', function (Blueprint $table) {
            $table->dropColumn(['token', 'referencia_tipo', 'referencia_id']);
        });
    }
};
