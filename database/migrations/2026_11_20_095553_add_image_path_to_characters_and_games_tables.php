<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('characters_and_games_tables', function (Blueprint $table) {
            Schema::table('characters', function (Blueprint $table) {
                $table->string('image_path', 2048)->nullable()->after('image_url');
            });

            Schema::table('games', function (Blueprint $table) {
                $table->string('image_path', 2048)->nullable()->after('image_url');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters_and_games_tables', function (Blueprint $table) {
            Schema::table('characters', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });

            Schema::table('games', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });
        });
    }
};
