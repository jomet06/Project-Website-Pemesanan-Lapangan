<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        $facilityIds = Facility::pluck('id_facilities')->toArray();

        $fields = [
            // ─── GOR Badminton (3 lapangan di dalam satu gedung) ─────────
            [
                'name_fields'    => 'GOR Badminton – Lapangan 1',
                'type_fields'    => 'Badminton',
                'description'    => 'Lapangan badminton standar PBSI dengan karpet vinyl anti-slip. Pencahayaan LED samping yang tidak silau. Berada di GOR utama lantai 1.',
                'price_per_hour' => 60000,
                'capacity'       => 6,
                'is_active'      => true,
                'facilities'     => [0, 1, 2],
            ],
            [
                'name_fields'    => 'GOR Badminton – Lapangan 2',
                'type_fields'    => 'Badminton',
                'description'    => 'Lapangan badminton standar PBSI dengan karpet vinyl anti-slip. Pencahayaan LED samping yang tidak silau. Berada di GOR utama lantai 1.',
                'price_per_hour' => 60000,
                'capacity'       => 6,
                'is_active'      => true,
                'facilities'     => [0, 1, 2],
            ],
            [
                'name_fields'    => 'GOR Badminton – Lapangan 3',
                'type_fields'    => 'Badminton',
                'description'    => 'Lapangan badminton standar PBSI dengan karpet vinyl anti-slip. Pencahayaan LED samping yang tidak silau. Berada di GOR utama lantai 1.',
                'price_per_hour' => 60000,
                'capacity'       => 6,
                'is_active'      => true,
                'facilities'     => [0, 1, 2],
            ],

            // ─── Area Futsal (2 lapangan) ─────────────────────────────────
            [
                'name_fields'    => 'Lapangan Futsal A',
                'type_fields'    => 'Futsal',
                'description'    => 'Lapangan futsal indoor dengan lantai vinyl taflex standar internasional. Gawang aluminium berstandar resmi, pencahayaan LED anti-silau, dan ruang tunggu ber-AC.',
                'price_per_hour' => 150000,
                'capacity'       => 14,
                'is_active'      => true,
                'facilities'     => [0, 1, 2, 3],
            ],
            [
                'name_fields'    => 'Lapangan Futsal B',
                'type_fields'    => 'Futsal',
                'description'    => 'Lapangan futsal indoor dengan lantai vinyl taflex standar internasional. Gawang aluminium berstandar resmi, pencahayaan LED anti-silau, dan ruang tunggu ber-AC.',
                'price_per_hour' => 150000,
                'capacity'       => 14,
                'is_active'      => true,
                'facilities'     => [0, 1, 2, 3],
            ],

            // ─── Lapangan Basket (1 lapangan) ────────────────────────────
            [
                'name_fields'    => 'Lapangan Basket',
                'type_fields'    => 'Basket',
                'description'    => 'Lapangan basket indoor dengan papan pantul akrilik premium dan lantai kayu parket. Papan skor digital, ring adjustable, dan garis lapangan standar NBA. Kapasitas penonton tersedia.',
                'price_per_hour' => 200000,
                'capacity'       => 15,
                'is_active'      => true,
                'facilities'     => [0, 1, 2, 4],
            ],
        ];

        foreach ($fields as $data) {
            $facilityIndices = $data['facilities'];
            unset($data['facilities']);

            $field = Field::create($data);

            $attachIds = array_values(array_filter(
                array_map(fn($i) => $facilityIds[$i] ?? null, $facilityIndices)
            ));
            if ($attachIds) {
                $field->facilities()->attach($attachIds);
            }
        }
    }
}
