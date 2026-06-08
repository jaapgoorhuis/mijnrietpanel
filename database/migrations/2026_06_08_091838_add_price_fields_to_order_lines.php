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
        if (! Schema::hasColumn('order_lines', 'price_per_m2')) {


            Schema::table('order_lines', function (Blueprint $table) {
                $table->decimal('price_per_m2', 10, 2)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */

};
