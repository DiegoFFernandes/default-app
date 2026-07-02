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
        Schema::table('comprovante', function (Blueprint $table) {
            $table->unsignedBigInteger('nr_lancamento')->nullable()->after('st_arquivo');
        });
    }

    public function down()
    {
        Schema::table('comprovante', function (Blueprint $table) {
            $table->dropColumn('nr_lancamento');
        });
    }
};
