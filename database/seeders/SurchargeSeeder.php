<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurchargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('surcharges')->insert([
           [
               'condition' => 'onder',
               'number' => 2500,
               'rule' => 'zaaglengte',
               'price' => 8,
               'name' => 'Lengte toeslag per zaaglengte (onder 2500mm)',
           ],
           [
               'condition' => 'onder',
               'number' => 50,
               'rule' => 'vierkantemeter',
               'price' => 350,
               'name' => 'Transportkosten (onder 50m²)',
           ]
       ]);
    }
}
