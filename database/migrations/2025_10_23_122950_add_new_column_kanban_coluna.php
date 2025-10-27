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
        Schema::table('kanban_colunas', function (Blueprint $table) {
            $table->string('color')->after('posicao')->default('6c757d')->nullable();
            $table->char('st_coluna', 1)->after('color')->default('P')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kanban_colunas', function (Blueprint $table) {
            $table->dropColumn('color');
            $table->dropColumn('st_coluna');
        });
    }
};
