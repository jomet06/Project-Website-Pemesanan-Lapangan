<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua ID fasilitas yang baru dibuat
        $facilityIds = Facility::pluck('id_facilities')->toArray();

        // Lapangan 1: Futsal Matras
        $field1 = Field::create([
            'name_fields'    => 'Lapangan Futsal Arena A (Matras)',
            'type_fields'    => 'Futsal',
            'description'    => 'Lapangan futsal standar internasional menggunakan jenis lantai matras interlock yang nyaman dan tidak licin. Cocok untuk turnamen maupun latihan rutin.',
            'price_per_hour' => 150000,
            'capacity'       => 12,
            'image'          => 'fields/sample-futsal.jpg', // path dummy
            'is_active'      => true,
        ]);
        // Pasangkan dengan fasilitas ID 1, 2, 3, dan 4
        $field1->facilities()->attach(array_slice($facilityIds, 0, 4));

        // Lapangan 2: Basket Indoor
        $field2 = Field::create([
            'name_fields'    => 'Grand Basketball Court (Indoor)',
            'type_fields'    => 'Basket',
            'description'    => 'Lapangan basket indoor dengan papan pantul acrilic premium dan lantai kayu parket orisinil yang meminimalisir risiko cedera.',
            'price_per_hour' => 200000,
            'capacity'       => 15,
            'image'          => 'fields/sample-basket.jpg',
            'is_active'      => true,
        ]);
        // Pasangkan dengan fasilitas ID 1, 2, 3, dan 5
        $field2->facilities()->attach([$facilityIds[0], $facilityIds[1], $facilityIds[2], $facilityIds[4]]);

        // Lapangan 3: Badminton
        $field3 = Field::create([
            'name_fields'    => 'Lapangan Badminton 1 (Karpet Premium)',
            'type_fields'    => 'Badminton',
            'description'    => 'Lapangan bulutangkis menggunakan karpet vinyl standar PBSI dengan pencahayaan lampu LED samping yang tidak silau saat smash.',
            'price_per_hour' => 60000,
            'capacity'       => 6,
            'image'          => 'fields/sample-badminton.jpg',
            'is_active'      => true,
        ]);
        // Pasangkan dengan fasilitas ID 1, 2, 3
        $field3->facilities()->attach(array_slice($facilityIds, 0, 3));
    }
}