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
        Schema::table('agents', function (Blueprint $table) {
            $table->decimal('profit_percentage', 5, 2)->default(50.00);
            $table->boolean('is_active')->default(true);
            $table->integer('balance')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('profit_percentage');
            $table->dropColumn('is_active');
            $table->dropColumn('balance');
        });
    }
};
