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
        Schema::table('wpp_sessoes', function (Blueprint $table) {
            $table->string('numero', 20)->nullable()->after('session_name')
                ->comment('Número pareado, preenchido quando a sessão fica CONNECTED');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wpp_sessoes', function (Blueprint $table) {
            $table->dropColumn('numero');
        });
    }
};
