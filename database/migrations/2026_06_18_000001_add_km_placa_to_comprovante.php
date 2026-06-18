<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprovante', function (Blueprint $table) {
            $table->integer('km')->nullable()->after('ds_observacao');
            $table->string('nr_placa', 10)->nullable()->after('km');
        });
    }

    public function down(): void
    {
        Schema::table('comprovante', function (Blueprint $table) {
            $table->dropColumn(['km', 'nr_placa']);
        });
    }
};
