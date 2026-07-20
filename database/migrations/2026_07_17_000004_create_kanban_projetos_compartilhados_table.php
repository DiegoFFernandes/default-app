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
        Schema::create('kanban_projetos_compartilhados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_projeto')->constrained('kanban_projetos');
            $table->foreignId('id_user')->constrained('users');
            $table->foreignId('id_user_proprietario')->constrained('users');
            $table->timestamps();

            // Evita compartilhar o mesmo projeto duas vezes com o mesmo usuario
            $table->unique(['id_projeto', 'id_user']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kanban_projetos_compartilhados');
    }
};
