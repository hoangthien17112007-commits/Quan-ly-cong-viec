<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::create([
            'name' => 'Hà Nội',
            'address' => 'Quận Ba Đình',
            'phone' => '0900000001'
        ]);

        Location::create([
            'name' => 'TP Hồ Chí Minh',
            'address' => 'Quận 1',
            'phone' => '0900000002'
        ]);

        Location::create([
            'name' => 'Đà Nẵng',
            'address' => 'Quận Hải Châu',
            'phone' => '0900000003'
        ]);
        Location::create([
            'name' => 'Hải Phòng',
            'address' => 'Quận Hồng Bàng',
            'phone' => '0900000004'
        ]);
        Location::create([
            'name' => 'Cần Thơ',
            'address' => 'Quận Ninh Kiều',
            'phone' => '0900000005'
        ]);
        Location::create([
            'name' => 'Nha Trang',
            'address' => 'Quận Nha Trang',
            'phone' => '0900000006'
        ]);
        Location::create([
            'name' => 'Vũng Tàu',
            'address' => 'Quận Vũng Tàu',
            'phone' => '0900000007'
        ]);
        Location::create([
            'name' => 'Huế',
            'address' => 'Quận Huế',
            'phone' => '0900000008'
        ]);
        Location::create([
            'name' => 'Quảng Ninh',
            'address' => 'Quận Hạ Long',
            'phone' => '0900000009'
        ]);
        Location::create([
            'name' => 'Đà Lạt',
            'address' => 'Quận Đà Lạt',
            'phone' => '0900000010'
        ]);

    }
}
