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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('klantnaam');
            $table->string('referentie');
            $table->string('aflever_straat');
            $table->string('order_ordered')->nullable();
            $table->string('project_naam');
            $table->string('aflever_postcode');
            $table->string('aflever_plaats');
            $table->string('aflever_land');
            $table->string('rietkleur');
            $table->string('toepassing');
            $table->string('merk_paneel');
            $table->string('kerndikte');
            $table->string('intaker');
            $table->integer('user_id');
            $table->integer('discount');
            $table->string('status');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
