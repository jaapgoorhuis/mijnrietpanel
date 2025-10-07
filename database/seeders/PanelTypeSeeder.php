<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PanelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('panel_types')->insert([
           [
               'name' => '60mm',

           ],
           [
               'name' => '80mm',

           ],
           [
               'name' => '90mm',

           ],
           [
               'name' => '100mm',

           ],
           [
               'name' => '120mm',

           ],
           [
               'name' => '140mm',

           ],
           [
               'name' => '150mm',

           ],
       ]);
    }
}
