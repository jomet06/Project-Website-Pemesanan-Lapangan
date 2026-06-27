<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $fields = Field::with('schedules')->get();
        
        $query = Schedule::with('field', 'booking.user');

        if ($request->has('filter_start_date') && $request->filter_start_date && $request->has('filter_end_date') && $request->filter_end_date) {
            $query->whereBetween('date', [$request->filter_start_date, $request->filter_end_date]);
        } elseif ($request->has('filter_date') && $request->filter_date) {
            $query->whereDate('date', $request->filter_date);
        } else {
            $query->whereDate('date', '>=', Carbon::today());
        }

        if ($request->has('filter_field_id') && $request->filter_field_id) {
            $query->where('field_id', $request->filter_field_id);
        }

        $schedules = $query->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('admin.schedules', compact('fields', 'schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id_fields',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'interval' => 'required|integer|min:1',
        ]);

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $intervalMinutes = (int) $request->interval;
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $fieldId = $request->field_id;

        $created = 0;

        for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
            $dateString = $d->format('Y-m-d');
            $current = $start->copy();

            while ($current->copy()->addMinutes($intervalMinutes) <= $end) {
                $slotStart = $current->format('H:i:s');
                $slotEnd = $current->copy()->addMinutes($intervalMinutes)->format('H:i:s');

                $exists = Schedule::where('field_id', $fieldId)
                    ->where('date', $dateString)
                    ->where('start_time', $slotStart)
                    ->exists();

                if (!$exists) {
                    Schedule::create([
                        'field_id' => $fieldId,
                        'date' => $dateString,
                        'start_time' => $slotStart,
                        'end_time' => $slotEnd,
                        'status_schedules' => 'Available',
                    ]);
                    $created++;
                }

                $current->addMinutes($intervalMinutes);
            }
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

    public function destroyAll(Request $request)
    {
        $query = Schedule::query();
        
        if ($request->has('filter_start_date') && $request->filter_start_date && $request->has('filter_end_date') && $request->filter_end_date) {
            $query->whereBetween('date', [$request->filter_start_date, $request->filter_end_date]);
        } elseif ($request->has('filter_date') && $request->filter_date) {
            $query->whereDate('date', $request->filter_date);
        } else {
            $query->whereDate('date', '>=', Carbon::today());
        }

        if ($request->has('filter_field_id') && $request->filter_field_id) {
            $query->where('field_id', $request->filter_field_id);
        }

        $deleted = $query->where('status_schedules', '!=', 'Booked')->delete();

        return back()->with('success', $deleted . ' jadwal berhasil dihapus (Jadwal yang sudah di-booking tidak ikut terhapus).');
    }
}
