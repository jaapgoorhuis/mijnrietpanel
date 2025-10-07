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
               'company_id' => '0',
               'reseller' => '0',
           ],
           [
               'rule_name' => 'Paneel 80mm',
               'panel_type' => '2',
               'price' => '45',
               'company_id' => '0',
               'reseller' => '0',
           ],
           [
               'rule_name' => 'Paneel 90mm',
               'panel_type' => '3',
               'price' => '45',
               'company_id' => '0',
               'reseller' => '0',
           ],
           [
               'rule_name' => 'Paneel 100mm',
               'panel_type' => '4',
               'price' => '50',
               'company_id' => '0',
               'reseller' => '0',
           ],
           [
               'rule_name' => 'Paneel 120mm',
               'panel_type' => '5',
               'price' => '55',
               'company_id' => '0',
               'reseller' => '0',
           ],
           [
               'rule_name' => 'Paneel 140mm',
               'panel_type' => '6',
               'price' => '60',
               'company_id' => '0',
               'reseller' => '0',
           ],
           [
               'rule_name' => 'Paneel 150mm',
               'panel_type' => '7',
               'price' => '65',
               'company_id' => '0',
               'reseller' => '0',
           ],

           [
               'rule_name' => 'Paneel 60mm',
               'panel_type' => '1',
               'price' => '40',
               'company_id' => '1',
               'reseller' => '1',
           ],
           [
               'rule_name' => 'Paneel 80mm',
               'panel_type' => '2',
               'price' => '45',
               'company_id' => '1',
               'reseller' => '1',
           ],
           [
               'rule_name' => 'Paneel 90mm',
               'panel_type' => '3',
               'price' => '45',
               'company_id' => '1',
               'reseller' => '1',
           ],
           [
               'rule_name' => 'Paneel 100mm',
               'panel_type' => '4',
               'price' => '50',
               'company_id' => '1',
               'reseller' => '1',
           ],
           [
               'rule_name' => 'Paneel 120mm',
               'panel_type' => '5',
               'price' => '55',
               'company_id' => '1',
               'reseller' => '1',
           ],
           [
               'rule_name' => 'Paneel 140mm',
               'panel_type' => '6',
               'price' => '60',
               'company_id' => '1',
               'reseller' => '1',
           ],
           [
               'rule_name' => 'Paneel 150mm',
               'panel_type' => '7',
               'price' => '65',
               'company_id' => '1',
               'reseller' => '1',
           ],
       ]);
    }
}
