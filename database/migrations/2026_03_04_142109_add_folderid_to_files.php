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
        Schema::table('detailFolders', function (Blueprint $table) {
            $table->integer('detailsFolder_id');
        });

        Schema::table('marketing', function (Blueprint $table) {
            $table->integer('marketingFolder_id');
        });

        Schema::table('documentation', function (Blueprint $table) {
            $table->integer('documentationFolder_id');
        });

        Schema::table('pricelist', function (Blueprint $table) {
            $table->integer('pricelistFolder_id');
        });
    }

    public function down(): void
    {
        Schema::table('detailFolders', function (Blueprint $table) {
            $table->dropColumn('detailFolder_id');
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
