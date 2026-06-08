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
        Schema::table('orders', function (Blueprint $table) {

            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->nullable()->after('status');
            }

            if (!Schema::hasColumn('orders', 'surcharges_total')) {
                $table->decimal('surcharges_total', 10, 2)->nullable()->after('subtotal');
            }

            if (!Schema::hasColumn('orders', 'vat_total')) {
                $table->decimal('vat_total', 10, 2)->nullable()->after('surcharges_total');
            }

            if (!Schema::hasColumn('orders', 'grand_total')) {
                $table->decimal('grand_total', 10, 2)->nullable()->after('vat_total');
            }

            if (!Schema::hasColumn('orders', 'prices_updated_at')) {
                $table->timestamp('prices_updated_at')->nullable()->after('grand_total');
            }
        });

        Schema::table('offerte', function (Blueprint $table) {

            if (!Schema::hasColumn('offerte', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->nullable()->after('status');
            }

            if (!Schema::hasColumn('offerte', 'surcharges_total')) {
                $table->decimal('surcharges_total', 10, 2)->nullable()->after('subtotal');
            }

            if (!Schema::hasColumn('offerte', 'vat_total')) {
                $table->decimal('vat_total', 10, 2)->nullable()->after('surcharges_total');
            }

            if (!Schema::hasColumn('offerte', 'grand_total')) {
                $table->decimal('grand_total', 10, 2)->nullable()->after('vat_total');
            }

            if (!Schema::hasColumn('offerte', 'prices_updated_at')) {
                $table->timestamp('prices_updated_at')->nullable()->after('grand_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'surcharges_total',
                'vat_total',
                'grand_total',
                'prices_updated_at',
            ]);
        });

        Schema::table('offerte', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'surcharges_total',
                'vat_total',
                'grand_total',
                'prices_updated_at',
            ]);
        });
    }
};
