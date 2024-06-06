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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('winner_player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->json('winning_numbers')->nullable();
            $table->json('draw_numbers')->nullable();
            $table->integer('winner_net_amount')->nullable();
            $table->timestamp('scheduled_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
