<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_mensagens', function (Blueprint $table) {
            $table->id();
            $table->enum('direcao', ['enviada', 'recebida']);
            $table->string('telefone'); // wa_id, ja normalizado (com DDI)
            $table->text('mensagem');
            $table->string('wamid')->nullable()->index(); // ID da mensagem na Meta - liga envio a status/leitura
            $table->string('status')->nullable(); // accepted/sent/delivered/read/failed (enviada) - nulo em recebida
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_mensagens');
    }
};
