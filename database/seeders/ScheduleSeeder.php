<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $fields = Field::all();
        
        // Loop untuk membuat jadwal hari ini dan besok
        $dates = [
            Carbon::today()->format('Y-m-d'),
            Carbon::tomorrow()->format('Y-m-d')
        ];

        // Definisi jam slot (per 1 jam atau per 2 jam sesuai durasi)
        $timeSlots = [
            ['start' => '15:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '17:00'],
            ['start' => '17:00', 'end' => '18:00'],
            ['start' => '19:00', 'end' => '20:00'],
            ['start' => '20:00', 'end' => '21:00'],
            ['start' => '21:00', 'end' => '22:00'],
        ];

        foreach ($fields as $field) {
            foreach ($dates as $date) {
                foreach ($timeSlots as $slot) {
                    Schedule::create([
                        'field_id'         => $field->id_fields,
                        'date'             => $date,
                        'start_time'       => $slot['start'],
                        'end_time'         => $slot['end'],
                        'status_schedules' => 'available', // Semua di-set tersedia di awal
                    ]);
                }
            }
        }
    }
}