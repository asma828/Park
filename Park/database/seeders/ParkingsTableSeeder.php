<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParkingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    DB::table('parkings')->insert([
        [
            "name" => "Central Parking",
            "location" => "123 Main Street",  
            "total_spaces" => 100,
            "available_spaces" => 100,  
        ],
        [
            "name" => "Mall Parking",
            "location" => "456 Shopping Avenue",  
            "total_spaces" => 200,
            "available_spaces" => 200,  
        ],
        [
            "name" => "Stadium Parking",
            "location" => "789 Sports Boulevard",  
            "total_spaces" => 300,
            "available_spaces" => 300,  
        ],
    ]);
}

}
