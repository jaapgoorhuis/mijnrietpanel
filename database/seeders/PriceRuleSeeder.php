<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('price_rules')->insert([
           [
               'rule_name' => 'Paneel 60mm',
               'panel_type' => '1',
               'price' => '40',

           ],
           [
               'rule_name' => 'Paneel 80mm',
               'panel_type' => '2',
               'price' => '45',

           ],
           [
               'rule_name' => 'Paneel 100mm',
               'panel_type' => '3',
               'price' => '50',

           ],
           [
               'rule_name' => 'Paneel 120mm',
               'panel_type' => '4',
               'price' => '55',

           ],
           [
               'rule_name' => 'Paneel 140mm',
               'panel_type' => '5',
               'price' => '60',

           ],
           [
               'rule_name' => 'Paneel 160mm',
               'panel_type' => '6',
               'price' => '65',

           ],
       ]);
    }
}
