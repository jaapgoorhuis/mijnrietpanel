<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Orders die vanuit een offerte zijn aangemaakt kregen nog de oude status
     * 'In behandeling' en waren daardoor niet in te plannen.
     */
    public function up(): void
    {
        DB::table('orders')
            ->where('status', 'In behandeling')
            ->whereNull('planned_start')
            ->update(['status' => 'Ongepland']);
    }

    public function down(): void
    {
        // Niet terug te draaien: we weten niet welke orders de oude status hadden.
    }
};
