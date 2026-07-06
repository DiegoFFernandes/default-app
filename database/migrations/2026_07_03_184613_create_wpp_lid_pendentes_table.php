<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wpp_lid_pendentes', function (Blueprint $table) {
            $table->string('lid')->primary();
            $table->string('pushname')->nullable();
            $table->text('ultimo_texto')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wpp_lid_pendentes');
    }
};
