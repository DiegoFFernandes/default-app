<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprovantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cd_user');
            $table->string('tp_despesa', 3);
            $table->decimal('vl_consumido', 10, 2);
            $table->text('ds_observacao')->nullable();
            $table->char('st_visto', 1)->default('N');
            $table->date('dt_despesa');
            $table->timestamps();

            $table->foreign('cd_user')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprovantes');
    }
};
