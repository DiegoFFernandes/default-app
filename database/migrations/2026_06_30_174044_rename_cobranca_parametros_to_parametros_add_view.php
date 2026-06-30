<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cobranca_parametros')) {
            Schema::rename('cobranca_parametros', 'parametros');
        }

        if (!Schema::hasColumn('parametros', 'view')) {
            Schema::table('parametros', function (Blueprint $table) {
                $table->string('view', 100)->after('id')->default('cobranca');
            });
        }

        DB::table('parametros')->whereNull('view')->orWhere('view', '')->update(['view' => 'cobranca']);

        $indexes = DB::select("SHOW INDEX FROM parametros WHERE Key_name = 'chave_unique'");
        if (!empty($indexes)) {
            Schema::table('parametros', function (Blueprint $table) {
                $table->dropUnique('chave_unique');
            });
        }

        $composite = DB::select("SHOW INDEX FROM parametros WHERE Key_name = 'parametros_view_chave_unique'");
        if (empty($composite)) {
            Schema::table('parametros', function (Blueprint $table) {
                $table->unique(['view', 'chave']);
            });
        }

        DB::statement('ALTER TABLE parametros MODIFY COLUMN `view` VARCHAR(100) NOT NULL');
    }

    public function down(): void
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->dropUnique(['view', 'chave']);
            $table->string('chave', 100)->unique();
            $table->dropColumn('view');
        });

        Schema::rename('parametros', 'cobranca_parametros');
    }
};
