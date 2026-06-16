<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with('field')->where('date', '>=', today());

        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status_schedules', $request->status);
        } else {
            $query->where('status_schedules', 'available');
        }

        $schedules = $query->orderBy('date')->orderBy('start_time')->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $schedules,
        ]);
    }
}
