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
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('categoria'); // UTILITY | MARKETING | AUTHENTICATION
            $table->string('idioma')->default('pt_BR');
            $table->json('componentes'); // header/body/footer no formato aceito pela Graph API
            $table->string('status')->default('rascunho'); // rascunho | enviado | aprovado | rejeitado
            $table->string('meta_template_id')->nullable();
            $table->text('motivo_rejeicao')->nullable();
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
        Schema::dropIfExists('whatsapp_templates');
    }
};
