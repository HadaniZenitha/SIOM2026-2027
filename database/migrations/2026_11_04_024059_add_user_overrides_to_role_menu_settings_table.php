<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('role_menu_settings', 'user_id')) {
            Schema::table('role_menu_settings', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('role_id')
                    ->constrained()
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasColumn('role_menu_settings', 'access_scope')) {
            Schema::table('role_menu_settings', function (Blueprint $table) {
                $table->string('access_scope', 100)->default('role')->after('user_id');
            });
        }

        Schema::table('role_menu_settings', function (Blueprint $table) {
            $table->unique(['role_id', 'menu_key', 'access_scope']);
        });

        Schema::table('role_menu_settings', function (Blueprint $table) {
            $table->dropUnique('role_menu_settings_role_id_menu_key_unique');
            $table->index(['user_id', 'menu_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The previous schema cannot represent user overrides alongside role defaults.
        DB::table('role_menu_settings')->whereNotNull('user_id')->delete();

        Schema::table('role_menu_settings', function (Blueprint $table) {
            $table->dropUnique('role_menu_settings_role_id_menu_key_access_scope_unique');
            $table->dropIndex(['user_id', 'menu_key']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('access_scope');
            $table->unique(['role_id', 'menu_key']);
        });
    }
};
