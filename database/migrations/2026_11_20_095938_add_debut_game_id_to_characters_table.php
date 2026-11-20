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
        Schema::table('characters', function (Blueprint $table) {
            Schema::table('characters', function (Blueprint $table) {
                $table->foreignId('debut_game_id')
                    ->nullable()
                    ->constrained('games')
                    ->nullOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            Schema::table('characters', function (Blueprint $table) {
                $table->dropConstrainedForeignId('debut_game_id');
            });
        });
    }
};
