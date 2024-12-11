<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class AlamatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('alamats')->insert([
            'user_id' => 1,
            'alamat' => 'Jl. Sumpah Pemuda 45',
            'province_id' => 9,
            'kota_id' => 23,
        ]);
    }
}
