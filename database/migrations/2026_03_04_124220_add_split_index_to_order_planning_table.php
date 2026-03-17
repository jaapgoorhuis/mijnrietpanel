<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('order_planning', function (Blueprint $table) {
            $table->integer('split_index')->nullable();
            $table->integer('split_total')->nullable();
        });
    }

    public function down()
    {
        Schema::table('order_planning', function (Blueprint $table) {
            $table->dropColumn(['split_index', 'split_total']);
        });
    }
};
