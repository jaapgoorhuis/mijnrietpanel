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
        Schema::create('order_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('rule');
            $table->decimal('price');
            $table->boolean('show_orderlist');

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_rules');
    }
};
