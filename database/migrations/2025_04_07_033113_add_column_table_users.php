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
        Schema::table('users', function (Blueprint $table) {
            $table->string('empresa')->after('email')->nullable();
            $table->timestamp('last_seen')->after('updated_at')->nullable();
            $table->bigInteger('phone')->after('empresa')->nullable();
            $table->bigInteger('cd_tipopessoa')->default(1)->after('phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('empresa');
            $table->dropColumn('phone');
            $table->dropColumn('last_seen');
        });
    }
};
