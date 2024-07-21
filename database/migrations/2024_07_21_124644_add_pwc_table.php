<?php

use App\Models\Cartela;
use App\Models\Game;
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
        Schema::create('pwc', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Game::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Cartela::class)->constrained()->cascadeOnDelete();
            $table->integer('winning_rows')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
