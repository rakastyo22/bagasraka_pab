<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProvinceSeeder extends Seeder
{
    public function run()
    {
        DB::table('provinces')->insert([
            ['province_id' => 1, 'province' => 'Aceh'],
            ['province_id' => 2, 'province' => 'Sumatera Utara'],
            ['province_id' => 3, 'province' => 'Jawa Tengah'],
            ['province_id' => 4, 'province' => 'Jawa Barat'],
            ['province_id' => 5, 'province' => 'Jawa Timur'],
            ['province_id' => 6, 'province' => 'DKI Jakarta'],
        ]);
    }
}
