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
        Schema::table('supervisor_comercial', function (Blueprint $table) {
            $table->char('libera_acima_param', 1)
                ->default('0')->after('ds_supervisorcomercial');
            $table->char('pc_permitida', 3)
                ->default('0')->after('libera_acima_param');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supervisor_comercial', function (Blueprint $table) {
            $table->dropColumn('libera_acima_param');
            $table->dropColumn('pc_permitida');
        });
    }
};
