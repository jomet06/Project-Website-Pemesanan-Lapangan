<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Schedule::with('field')->latest();

        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status_schedules', $request->status);
        }

        $schedules = $query->paginate(15);
        $fields    = Field::active()->get();

        return view('admin.schedules.index', compact('schedules', 'fields'));
    }

    public function create()
    {
        $fields = Field::active()->get();
        return view('admin.schedules.create', compact('fields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_id'         => 'required|exists:fields,id_fields',
            'date'             => 'required|date|after_or_equal:today',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'status_schedules' => 'required|in:available,booked,closed',
        ]);

        // Check for overlapping schedules
        $overlap = Schedule::where('field_id', $validated['field_id'])
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
            })->exists();

        if ($overlap) {
            return back()->withErrors(['time' => 'Jadwal bertabrakan dengan jadwal yang sudah ada.'])->withInput();
        }

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit(Schedule $schedule)
    {
        $fields = Field::active()->get();
        return view('admin.schedules.edit', compact('schedule', 'fields'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'field_id'         => 'required|exists:fields,id_fields',
            'date'             => 'required|date',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'status_schedules' => 'required|in:available,booked,closed',
        ]);
        // Check for overlapping schedules (exclude current schedule being edited)
        $overlap = Schedule::where('field_id', $validated['field_id'])
            ->where('date', $validated['date'])
            ->where('id_schedules', '!=', $schedule->id_schedules)
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
            })->exists();

        if ($overlap) {
            return back()->withErrors(['time' => 'Jadwal bertabrakan dengan jadwal yang sudah ada.'])->withInput();
        }

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->booking) {
            return back()->withErrors(['delete' => 'Tidak dapat menghapus jadwal yang sudah dibooking.']);
        }
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}