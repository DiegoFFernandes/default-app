<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('comprovante', function (Blueprint $table) {
            $table->char('st_arquivo', 1)->default('N')->after('st_importado_fb');
        });
    }

    public function down()
    {
        Schema::table('comprovante', function (Blueprint $table) {
            $table->dropColumn('st_arquivo');
        });
    }
};
