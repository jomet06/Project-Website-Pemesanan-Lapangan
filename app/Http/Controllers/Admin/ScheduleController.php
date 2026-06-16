<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $fields = Field::with('schedules')->get();
        $schedules = Schedule::with('field', 'booking.user')
            ->whereDate('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('admin.schedules', compact('fields', 'schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id_fields',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'interval' => 'required|integer|min:1',
        ]);

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $intervalMinutes = $request->interval;
        $date = $request->date;
        $fieldId = $request->field_id;

        $current = $start->copy();
        $created = 0;

        while ($current->copy()->addMinutes($intervalMinutes) <= $end) {
            $slotStart = $current->format('H:i:s');
            $slotEnd = $current->copy()->addMinutes($intervalMinutes)->format('H:i:s');

            // Check if schedule already exists for this time slot
            $exists = Schedule::where('field_id', $fieldId)
                ->where('date', $date)
                ->where('start_time', $slotStart)
                ->exists();

            if (!$exists) {
                Schedule::create([
                    'field_id' => $fieldId,
                    'date' => $date,
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd,
                    'status_schedules' => 'Available',
                ]);
                $created++;
            }

            $current->addMinutes($intervalMinutes);
        }

        if ($created > 0) {
            return redirect()->route('admin.schedules')->with('success', "$created jadwal berhasil ditambahkan.");
        }

        return redirect()->route('admin.schedules')->with('info', 'Tidak ada jadwal baru (mungkin sudah ada).');
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'date' => 'required|date',
            'status_schedules' => 'required|in:Available,Booked,Locked',
        ]);

        $schedule->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'date' => $request->date,
            'status_schedules' => $request->status_schedules,
        ]);

        return redirect()->route('admin.schedules')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $schedule = Schedule::with('booking')->findOrFail($id);

        if ($schedule->booking && $schedule->booking->status_bookings === 'Paid') {
            return back()->with('error', 'Tidak dapat menghapus jadwal yang sudah dibooking dan dibayar.');
        }

        $schedule->delete();

        return redirect()->route('admin.schedules')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $schedule = Schedule::with('booking')->findOrFail($id);

        if ($schedule->booking && $schedule->booking->status_bookings !== 'Cancelled') {
            return back()->with('error', 'Tidak dapat mengubah status jadwal yang sedang dibooking.');
        }

        $newStatus = $schedule->status_schedules === 'Available' ? 'Locked' : 'Available';
        $schedule->update(['status_schedules' => $newStatus]);

        return redirect()->route('admin.schedules')->with('success', "Status jadwal diubah menjadi $newStatus.");
    }
}
