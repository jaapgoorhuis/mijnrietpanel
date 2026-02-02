<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['offerte', 'orders', 'details', 'marketing', 'pricelist', 'documentation', 'companys', 'users'];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'lang')) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                    // Bepaal default voor users en companys
                        $tableBlueprint->string('lang')->default('nl');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['offerte', 'orders', 'details', 'marketing', 'pricelist', 'documentation', 'companys', 'users'];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'lang')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->dropColumn('lang');
                });
            }
        }
    }
};
