<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offerte_lines', function (Blueprint $table) {
            $table->boolean('waterstop_enabled')
                ->default(false)
                ->after('fillCb');

            $table->unsignedSmallInteger('waterstop_type')
                ->nullable()
                ->after('waterstop_enabled');

            $table->unsignedSmallInteger('waterstop_vertical')
                ->nullable()
                ->after('waterstop_type');

            $table->smallInteger('waterstop_horizontal')
                ->nullable()
                ->after('waterstop_vertical');
        });
    }

    public function down(): void
    {
        Schema::table('offerte_lines', function (Blueprint $table) {
            $table->dropColumn([
                'waterstop_enabled',
                'waterstop_type',
                'waterstop_vertical',
                'waterstop_horizontal',
            ]);
        });
    }
};
