<?php

namespace Database\Seeders;

use App\Models\KerndikteColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KerndikteColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            '60mm' => 'white',
            '80mm' => 'yellow',
            '90mm' => 'grey',
            '100mm' => 'black',
            '120mm' => 'blue',
            '140mm' => 'orange',
            '150mm' => 'green',
        ];

        foreach ($colors as $kerndikte => $color) {
            KerndikteColor::updateOrCreate(['kerndikte' => $kerndikte], ['color' => $color]);
        }
    }
}
