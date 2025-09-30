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
        Schema::create('offerte_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('offerte_id');
            $table->string('fillCb');
            $table->string('fillLb');
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
        Schema::dropIfExists('offerte_lines');
    }
};
