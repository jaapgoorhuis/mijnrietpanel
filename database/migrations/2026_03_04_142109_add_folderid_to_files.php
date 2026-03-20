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
        Schema::table('details', function (Blueprint $table) {
            $table->integer('detail_category_id');
        });

        Schema::table('marketing', function (Blueprint $table) {
            $table->integer('marketing_category_id');
        });

        Schema::table('documentation', function (Blueprint $table) {
            $table->integer('documentation_category_id');
        });

        Schema::table('pricelist', function (Blueprint $table) {
            $table->integer('pricelist_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('details', function (Blueprint $table) {
            $table->dropColumn('detailsFolder_id');
        });

        Schema::table('marketing', function (Blueprint $table) {
            $table->dropColumn('marketingFolder_id');
        });

        Schema::table('documentation', function (Blueprint $table) {
            $table->dropColumn('documentationFolder_id');
        });

        Schema::table('pricelist', function (Blueprint $table) {
            $table->dropColumn('pricelistFolder_id');
        });
    }
};
