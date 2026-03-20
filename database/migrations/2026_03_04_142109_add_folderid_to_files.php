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
            $table->integer('marketing_folder_id');
        });
    }

    public function down(): void
    {
        Schema::table('details', function (Blueprint $table) {
            $table->dropColumn('detail_category_id');
        });

        Schema::table('marketing', function (Blueprint $table) {
            $table->dropColumn('marketing_folder_id');
        });
    }
};
