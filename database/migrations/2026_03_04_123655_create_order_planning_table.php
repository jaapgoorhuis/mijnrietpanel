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
        Schema::create('order_planning', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->date('planned_date');
            $table->decimal('planned_m2', 10, 2); // hoeveelheid m² gepland voor die dag
            $table->timestamps();

            $table->unique(['order_id', 'planned_date']); // 1 order per dag
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_planning');
    }
};
