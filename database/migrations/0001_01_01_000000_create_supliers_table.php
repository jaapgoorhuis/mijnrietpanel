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
        Schema::create('supliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('suplier_name')->nullable();
            $table->string('suplier_straat')->nullable();
            $table->string('suplier_land')->nullable();
            $table->string('suplier_postcode')->nullable();
            $table->string('suplier_plaats')->nullable();
            $table->string('suplier_email')->nullable();
            $table->integer('status');
            $table->integer('werkende_breedte');
            $table->integer('toepassing_dak');
            $table->integer('toepassing_wand');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supliers');
    }
};
