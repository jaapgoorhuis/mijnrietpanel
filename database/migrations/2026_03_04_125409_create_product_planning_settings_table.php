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
        Schema::create('product_planning_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('max_m2_per_day', 8, 2)->default(2); // maximaal m² per dag
            $table->json('blocked_days')->nullable(); // bv. ["Saturday","Sunday"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_planning_settings');
    }
};
