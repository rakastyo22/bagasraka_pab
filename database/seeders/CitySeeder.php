<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->insert([
            [
                'id' => 1,
                'city_id' => 1,
                'province_id' => 7,
                'province' => 'Aceh',
                'type' => 'Kota',
                'city_name' => 'Banda Aceh',
                'postal_code' => '23122',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'city_id' => 2,
                'province_id' => 7,
                'province' => 'Aceh',
                'type' => 'Kota',
                'city_name' => 'Sabang',
                'postal_code' => '24411',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'city_id' => 3,
                'province_id' => 3,
                'province' => 'Jawa Tengah',
                'type' => 'Kota',
                'city_name' => 'Semarang',
                'postal_code' => '50111',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'city_id' => 4,
                'province_id' => 6,
                'province' => 'DKI Jakarta',
                'type' => 'Kota',
                'city_name' => 'Jakarta Pusat',
                'postal_code' => '10110',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'city_id' => 5,
                'province_id' => 3,
                'province' => 'Jawa Tengah',
                'type' => 'Kota',
                'city_name' => 'Demak',
                'postal_code' => '59511',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
