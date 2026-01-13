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
        Schema::table('offerte', function (Blueprint $table) {
            $table->text('requested_delivery_date')->nullable();;
            $table->text('delivery_date')->nullable();
            $table->longText('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offerte', function (Blueprint $table) {
            $table->dropColumn('requested_delivery_date');
            $table->dropColumn('delivery_date');
            $table->dropColumn('comment');
        });
    }
};
