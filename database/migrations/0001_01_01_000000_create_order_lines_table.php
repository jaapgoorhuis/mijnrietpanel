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
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('rietkleur');
            $table->string('toepassing');
            $table->string('merk_paneel');
            $table->string('fillCb');
            $table->string('fillLb');
            $table->string('kerndikte');
            $table->string('fillTotaleLengte');
            $table->string('aantal');
            $table->integer('user_id');
            $table->string('m2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
