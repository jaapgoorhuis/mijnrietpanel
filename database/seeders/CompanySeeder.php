<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('companys')->insert([
           [
               'bedrijfsnaam' => 'Rietpanel',
               'discount' => '0',
               'straat' => 'Nijverheidsweg 59',
               'postcode' => '3771 ME',
               'plaats' => 'Barneveld',
               'is_reseller' => 1,
               'created_at' => '2025-09-29 15:12:37',
           ]
       ]);
    }
}
