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
        Schema::create('offerte', function (Blueprint $table) {
            $table->id();
            $table->integer('offerte_id');
            $table->string('klantnaam');
            $table->string('referentie');
            $table->string('status');
            $table->string('project_naam');
            $table->string('aflever_straat');
            $table->string('aflever_postcode');
            $table->string('aflever_plaats');
            $table->string('aflever_land');
            $table->string('intaker');
            $table->string('rietkleur');
            $table->string('toepassing');
            $table->string('merk_paneel');
            $table->string('kerndikte');
            $table->integer('user_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offerte');
    }
};
