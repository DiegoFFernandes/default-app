<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprovante', function (Blueprint $table) {
            $table->string('nm_solicitante', 255)->nullable()->after('cd_pessoa');
        });
    }

    public function down(): void
    {
        Schema::table('comprovante', function (Blueprint $table) {
            $table->dropColumn('nm_solicitante');
        });
    }
};
