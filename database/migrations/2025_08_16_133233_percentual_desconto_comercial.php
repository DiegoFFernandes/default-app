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
        Schema::create('percentual_desconto_comercial', function (Blueprint $table) {
            $table->id();
            $table->decimal('perc_desconto_min', 5, 2); // Referência ao percentual de desconto Minimo
            $table->decimal('perc_desconto_max', 5, 2); // Referência ao percentual de desconto Maximo
            $table->char('tp_cargo', 1); // tipo de cargo G = Gerente, S = Supervisor, A = Automatico

            $table->foreignId('cd_usuario'); // Referência ao usuário que criou o registro
            $table->foreign('cd_usuario')->references('id')->on('users');
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
        Schema::dropIfExists('percentual_desconto_comercial');
    }
};
