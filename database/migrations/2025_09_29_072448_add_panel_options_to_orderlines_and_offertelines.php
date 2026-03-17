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
        Schema::table('order_lines', function (Blueprint $table) {
            $table->integer('lb')->nullable();
            $table->integer('nokafschuining')->nullable();
            $table->integer('vrije_ruimte_1')->nullable();
            $table->integer('vrije_ruimte_2')->nullable();

        });

        Schema::table('offerte_lines', function (Blueprint $table) {
            $table->integer('lb')->nullable();
            $table->integer('nokafschuining')->nullable();
            $table->integer('vrije_ruimte_1')->nullable();
            $table->integer('vrije_ruimte_2')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_lines', function (Blueprint $table) {
            $table->dropColumn('lb');
            $table->dropColumn('nokafschuining');
            $table->dropColumn('vrije_ruimte_1');
            $table->dropColumn('vrije_ruimte_2');
        });

        Schema::table('offerte_lines', function (Blueprint $table) {
            $table->dropColumn('lb');
            $table->dropColumn('nokafschuining');
            $table->dropColumn('vrije_ruimte_1');
            $table->dropColumn('vrije_ruimte_2');
        });
    }
};
