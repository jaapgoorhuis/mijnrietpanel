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
        Schema::table('product_planning_settings', function (Blueprint $table) {
            $table->json('blocked_dates')->nullable()->after('blocked_days');
        });
    }

    public function down(): void
    {
        Schema::table('product_planning_settings', function (Blueprint $table) {
            $table->dropColumn('blocked_dates');
        });
    }
};
