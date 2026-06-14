<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            ['name_facilities' => 'Wi-Fi Gratis', 'icon' => 'wifi'],
            ['name_facilities' => 'Kamar Mandi / Shower', 'icon' => 'shower'],
            ['name_facilities' => 'Tempat Parkir Luas', 'icon' => 'parking'],
            ['name_facilities' => 'Kantin / Cafe', 'icon' => 'utensils'],
            ['name_facilities' => 'Locker Room', 'icon' => 'lock'],
            ['name_facilities' => 'Sewa Sepatu & Rompi', 'icon' => 'tshirt'],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }
    }
}