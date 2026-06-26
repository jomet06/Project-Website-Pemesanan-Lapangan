<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(Request $request)
    {
        $query = Field::with('facilities');

        if ($location = $request->location) {
            $query->where('description', 'like', '%' . $location . '%');
        }

        if ($sports = $request->sports) {
            $query->whereIn('type_fields', (array) $sports);
        }

        if ($maxPrice = $request->max_price) {
            $query->where('price_per_hour', '<=', (int) $maxPrice);
        }

        if ($sort = $request->sort) {
            if ($sort === 'Harga Terendah') {
                $query->orderBy('price_per_hour', 'asc');
            } elseif ($sort === 'Harga Tertinggi') {
                $query->orderBy('price_per_hour', 'desc');
            } elseif ($sort === 'Terpopuler') {
                $query->withCount('schedules')->orderBy('schedules_count', 'desc');
            } elseif ($sort === 'Terbaru') {
                $query->latest();
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
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