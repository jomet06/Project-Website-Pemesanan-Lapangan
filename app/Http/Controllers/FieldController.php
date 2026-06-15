<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(Request $request)
    {
        $query = Field::with('facilities')->latest();

        if ($location = $request->location) {
            $query->where('description', 'like', '%' . $location . '%');
        }

        if ($sports = $request->sports) {
            $query->whereIn('type_fields', (array) $sports);
        }

        if ($maxPrice = $request->max_price) {
            $query->where('price_per_hour', '<=', (int) $maxPrice);
        }

        $fields = $query->paginate(6)->withQueryString();

        return view('fields.index', compact('fields'));
    }

    public function show(Field $field)
    {
        // TAMBAHKAN 'schedules' di sini
        $field->load(['facilities', 'schedules']);
        
        return view('fields.show', compact('field'));
    }
}