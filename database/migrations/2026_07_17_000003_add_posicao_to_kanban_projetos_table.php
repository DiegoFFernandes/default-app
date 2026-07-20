<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('kanban_projetos', function (Blueprint $table) {
            $table->integer('posicao')->after('color')->nullable();
        });

        // Preenche a posicao dos projetos existentes na ordem atual (por id)
        DB::table('kanban_projetos')->orderBy('id')->get()->each(function ($projeto, $index) {
            DB::table('kanban_projetos')->where('id', $projeto->id)->update(['posicao' => $index + 1]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kanban_projetos', function (Blueprint $table) {
            $table->dropColumn('posicao');
        });
    }
};
