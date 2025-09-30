<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('supliers')->insert([
           [
               'name' => 'Kingspan',
               'suplier_name' => 'Kingspan',
               'suplier_straat'=> '',
               'suplier_postcode' => '',
               'suplier_land' => '',
               'suplier_plaats' => '',
               'status' => 1,
               'werkende_breedte' => '1000',
               'toepassing_wand' => 1,
               'toepassing_dak' => 1,
               'suplier_email' => '',
           ],
           [
               'name' => 'Falk nog niet beschikbaar',
               'status' => 0,
               'suplier_name' => 'Falk',
               'suplier_straat'=> '',
               'suplier_postcode' => '',
               'suplier_land' => '',
               'suplier_plaats' => '',
               'werkende_breedte' => '1060',
               'toepassing_wand' => 1,
               'toepassing_dak' => 0,
               'suplier_email' => '',
           ],
           [
               'name' => 'Joriside nog niet beschikbaar',
               'suplier_name' => 'Joriside',
               'suplier_straat'=> '',
               'suplier_postcode' => '',
               'suplier_land' => '',
               'suplier_plaats' => '',
               'status' => 0,
               'werkende_breedte' => '1000',
               'toepassing_wand' => 1,
               'toepassing_dak' => 0,
               'suplier_email' => '',
           ],
           [
               'name' => 'SAB profiel nog niet beschikbaar',
               'suplier_name' => 'SAB Profiel',
               'suplier_straat'=> '',
               'suplier_postcode' => '',
               'suplier_land' => '',
               'suplier_plaats' => '',
               'status' => 0,
               'werkende_breedte' => '1000',
               'toepassing_wand' => 1,
               'toepassing_dak' => 0,
               'suplier_email' => '',
           ],
       ]);
    }
}
