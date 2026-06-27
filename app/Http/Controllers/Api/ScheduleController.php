<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal.
     */
    public function index(Request $request)
    {
        $query = Schedule::with('field');

        if ($fieldId = $request->field_id) {
            $query->where('field_id', $fieldId);
        }

        if ($date = $request->date) {
            $query->where('date', $date);
        }

        $schedules = $query->orderBy('date')->orderBy('start_time')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar jadwal berhasil diambil',
            'data'    => $schedules
        ], 200);
    }
}
