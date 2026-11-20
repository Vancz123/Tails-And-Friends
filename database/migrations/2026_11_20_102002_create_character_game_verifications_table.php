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
        Schema::create('character_game_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('character_id');
            $table->foreignId('game_id');
            $table->timestamp('verified_at');
            $table->timestamps();

            $table->foreign(['character_id', 'game_id'])
                ->references(['character_id', 'game_id'])
                ->on('character_game')
                ->cascadeOnDelete();
            $table->unique(['user_id', 'character_id', 'game_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_game_verifications');
    }
};
