<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    /**
     * Menampilkan daftar lapangan.
     */
    public function index(Request $request)
    {
        $query = Field::with('facilities');

        if ($location = $request->location) {
            $query->where(function ($q) use ($location) {
                $q->where('address', 'like', '%' . $location . '%')
                  ->orWhere('description', 'like', '%' . $location . '%');
            });
        }

        if ($sports = $request->sports) {
            $query->whereIn('type_fields', (array) $sports);
        }

        if ($maxPrice = $request->max_price) {
            $query->where('price_per_hour', '<=', (int) $maxPrice);
        }

        if ($sort = $request->sort) {
            if ($sort === 'lowest_price' || $sort === 'Harga Terendah') {
                $query->orderBy('price_per_hour', 'asc');
            } elseif ($sort === 'highest_price' || $sort === 'Harga Tertinggi') {
                $query->orderBy('price_per_hour', 'desc');
            } elseif ($sort === 'popular' || $sort === 'Terpopuler') {
                $query->withCount('schedules')->orderBy('schedules_count', 'desc');
            } elseif ($sort === 'newest' || $sort === 'Terbaru') {
                $query->latest();
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $fields = $query->paginate(10)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Daftar lapangan berhasil diambil',
            'data'    => $fields
        ], 200);
    }

    /**
     * Menampilkan detail lapangan spesifik.
     */
    public function show($id)
    {
        $field = Field::with(['facilities', 'schedules'])->find($id);

        if (!$field) {
            return response()->json([
                'success' => false,
                'message' => 'Lapangan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail lapangan',
            'data'    => $field
        ], 200);
    }
}
