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
            $table->longText('cropimage')->nullable();
        });

        Schema::table('documentation', function (Blueprint $table) {
            $table->longText('cropimage')->nullable();
        });

        Schema::table('marketing', function (Blueprint $table) {
            $table->longText('cropimage')->nullable();
        });


    }

    public function down(): void
    {
        Schema::table('details', function (Blueprint $table) {
            $table->dropColumn('cropimage');
        });

        Schema::table('documentation', function (Blueprint $table) {
            $table->dropColumn('cropimage');
        });

        Schema::table('marketing', function (Blueprint $table) {
            $table->dropColumn('cropimage');
        });


    }
};
