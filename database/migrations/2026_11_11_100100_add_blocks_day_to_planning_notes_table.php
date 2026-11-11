<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planning_notes', function (Blueprint $table) {
            $table->boolean('blocks_day')->default(false)->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('planning_notes', function (Blueprint $table) {
            $table->dropColumn('blocks_day');
        });
    }
};
