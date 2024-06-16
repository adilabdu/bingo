<?php

use App\Models\User;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->float('amount');
            $table->unsignedBigInteger('from')->nullable();
            $table->unsignedBigInteger('to')->nullable();
            $table->timestamps();

            $table->foreign('to')
                ->references('id')
                ->on('users');

            $table->foreign('from')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
