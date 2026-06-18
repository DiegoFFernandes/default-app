<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop any FK referencing cd_user via raw SQL (avoid Blueprint name-mangling)
        $fks = DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'comprovante'
              AND REFERENCED_TABLE_NAME IS NOT NULL
              AND COLUMN_NAME = 'cd_user'
        ");

        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `comprovante` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // Rename column if it hasn't been renamed yet
        if (Schema::hasColumn('comprovante', 'cd_user')) {
            Schema::table('comprovante', function (Blueprint $table) {
                $table->renameColumn('cd_user', 'cd_user_lanc');
            });
        }

        // Re-add FK and add cd_pessoa
        Schema::table('comprovante', function (Blueprint $table) {
            $existing = DB::select("
                SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'comprovante'
                  AND REFERENCED_TABLE_NAME = 'users'
                  AND COLUMN_NAME = 'cd_user_lanc'
            ");

            if (empty($existing)) {
                $table->foreign('cd_user_lanc')->references('id')->on('users');
            }

            if (!Schema::hasColumn('comprovante', 'cd_pessoa')) {
                $table->unsignedBigInteger('cd_pessoa')->nullable()->after('cd_user_lanc');
            }
        });
    }

    public function down(): void
    {
        $fks = DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'comprovante'
              AND REFERENCED_TABLE_NAME IS NOT NULL
              AND COLUMN_NAME = 'cd_user_lanc'
        ");

        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `comprovante` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        Schema::table('comprovante', function (Blueprint $table) {
            if (Schema::hasColumn('comprovante', 'cd_pessoa')) {
                $table->dropColumn('cd_pessoa');
            }
        });

        if (Schema::hasColumn('comprovante', 'cd_user_lanc')) {
            Schema::table('comprovante', function (Blueprint $table) {
                $table->renameColumn('cd_user_lanc', 'cd_user');
            });
        }

        Schema::table('comprovante', function (Blueprint $table) {
            $table->foreign('cd_user')->references('id')->on('users');
        });
    }
};
