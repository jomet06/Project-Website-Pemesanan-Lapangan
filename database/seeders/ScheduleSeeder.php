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

        // Generate schedules for today + 13 days ahead (2 weeks)
        $dates = collect(range(0, 13))->map(fn($d) => Carbon::today()->addDays($d)->format('Y-m-d'));

        $timeSlots = [
            ['start' => '07:00', 'end' => '08:00'],
            ['start' => '08:00', 'end' => '09:00'],
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '11:00'],
            ['start' => '13:00', 'end' => '14:00'],
            ['start' => '14:00', 'end' => '15:00'],
            ['start' => '15:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '17:00'],
            ['start' => '18:00', 'end' => '19:00'],
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
                        'status_schedules' => 'available',
                    ]);
                }
            }
        }
    }
}
