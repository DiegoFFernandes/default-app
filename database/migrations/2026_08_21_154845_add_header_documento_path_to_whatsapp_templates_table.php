<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_templates', function (Blueprint $table) {
            // PDF de amostra guardado localmente (o header do tipo Documento
            // exige um exemplo pra Meta aprovar) - o handle que a API pede fica
            // gerado sob demanda a cada envio, porque ele expira.
            $table->string('header_documento_path')->nullable()->after('componentes');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_templates', function (Blueprint $table) {
            $table->dropColumn('header_documento_path');
        });
    }
};
