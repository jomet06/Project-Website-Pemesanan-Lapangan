<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Facility;

class GuestController extends Controller
{
    public function home()
    {
        $fields     = Field::with('facilities')->active()->latest()->limit(6)->get();
        $facilities = Facility::withCount('fields')->get();
        $sportTypes = Field::active()->distinct()->pluck('type_fields')->filter();
        return view('guest.home', compact('fields', 'facilities', 'sportTypes'));
    }

    public function fields(\Illuminate\Http\Request $request)
    {
        $query = Field::with('facilities')->active();

        if ($request->filled('type')) {
            $query->where('type_fields', $request->type);
        }
        if ($request->filled('search')) {
            $query->where('name_fields', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_hour', '<=', $request->max_price);
        }

        $fields     = $query->paginate(9);
        $sportTypes = Field::active()->distinct()->pluck('type_fields')->filter();
        return view('guest.fields', compact('fields', 'sportTypes'));
    }

    public function showField(Field $field)
    {
        $field->load('facilities');
        $schedules = \App\Models\Schedule::where('field_id', $field->id_fields)
            ->where('date', '>=', today())
            ->where('status_schedules', 'available')
            ->orderBy('date')->orderBy('start_time')
            ->get()->groupBy('date');
        return view('guest.field-detail', compact('field', 'schedules'));
    }

    public function about()  { return view('guest.about'); }
    public function contact(){ return view('guest.contact'); }
}