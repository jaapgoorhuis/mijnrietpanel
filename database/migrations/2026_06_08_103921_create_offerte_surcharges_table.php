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
        Schema::create('offerte_surcharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerte_id')->constrained('offerte')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('rule')->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offerte_surcharges');
    }
};
