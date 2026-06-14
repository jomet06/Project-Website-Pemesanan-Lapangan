<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    /**
     * @OA\Get(path="/api/fields", summary="Get all active fields")
     */
    public function index(Request $request)
    {
        $query = Field::with('facilities')->active();

        if ($request->filled('type')) {
            $query->where('type_fields', $request->type);
        }
        if ($request->filled('search')) {
            $query->where('name_fields', 'like', '%' . $request->search . '%');
        }

        $fields = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $fields,
        ]);
    }

    public function show(Field $field)
    {
        $field->load(['facilities', 'schedules' => function ($q) {
            $q->where('date', '>=', today())
              ->where('status_schedules', 'available')
              ->orderBy('date')
              ->orderBy('start_time');
        }]);

        return response()->json([
            'success' => true,
            'data'    => $field,
        ]);
    }
}