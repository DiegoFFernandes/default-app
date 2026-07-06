<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wpp_parametros', function (Blueprint $table) {
            $table->string('chave')->primary();
            $table->text('valor')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        DB::table('wpp_parametros')->insert([
            ['chave' => 'wpp_ia_ativo', 'valor' => '0'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('wpp_parametros');
    }
};
