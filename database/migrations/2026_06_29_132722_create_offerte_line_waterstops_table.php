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
        Schema::create('offerte_line_waterstops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerte_line_id')->constrained('offerte_lines')->cascadeOnDelete();
            $table->unsignedSmallInteger('type');
            $table->unsignedSmallInteger('vertical');
            $table->smallInteger('horizontal')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offerte_line_waterstops');
    }
};
