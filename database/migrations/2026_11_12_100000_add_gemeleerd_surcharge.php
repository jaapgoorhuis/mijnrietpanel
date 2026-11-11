<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('surcharges')->where('rule', 'gemeleerd')->exists()) {
            return;
        }

        DB::table('surcharges')->insert([
            'condition' => '0',
            'number' => 0,
            'rule' => 'gemeleerd',
            'price' => 25,
            'name' => 'Meerprijs Gemêleerd riet (per element)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('surcharges')->where('rule', 'gemeleerd')->delete();
    }
};
