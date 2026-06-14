<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::with('facilities')->latest()->paginate(10);
        return view('admin.fields.index', compact('fields'));
    }

    public function create()
    {
        $facilities = Facility::all();
        return view('admin.fields.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_fields'   => 'required|string|max:255',
            'type_fields'   => 'nullable|string|max:100',
            'description'   => 'required|string',
            'price_per_hour'=> 'required|numeric|min:0',
            'capacity'      => 'required|integer|min:1',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'     => 'boolean',
            'facilities'    => 'nullable|array',
            'facilities.*'  => 'exists:facilities,id_facilities',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('fields', 'public');
        }

        $field = Field::create($validated);

        if ($request->filled('facilities')) {
            $field->facilities()->sync($request->facilities);
        }

        return redirect()->route('admin.fields.index')->with('success', 'Lapangan berhasil ditambahkan!');
    }

    public function show(Field $field)
    {
        $field->load(['facilities', 'schedules' => fn($q) => $q->latest()->limit(10)]);
        return view('admin.fields.show', compact('field'));
    }

    public function edit(Field $field)
    {
        $facilities = Facility::all();
        $selectedFacilities = $field->facilities->pluck('id_facilities')->toArray();
        return view('admin.fields.edit', compact('field', 'facilities', 'selectedFacilities'));
    }

    public function update(Request $request, Field $field)
    {
        $validated = $request->validate([
            'name_fields'   => 'required|string|max:255',
            'type_fields'   => 'nullable|string|max:100',
            'description'   => 'required|string',
            'price_per_hour'=> 'required|numeric|min:0',
            'capacity'      => 'required|integer|min:1',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'     => 'boolean',
            'facilities'    => 'nullable|array',
            'facilities.*'  => 'exists:facilities,id_facilities',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($field->image) {
                Storage::disk('public')->delete($field->image);
            }
            $validated['image'] = $request->file('image')->store('fields', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $field->update($validated);
        $field->facilities()->sync($request->facilities ?? []);

        return redirect()->route('admin.fields.index')->with('success', 'Lapangan berhasil diperbarui!');
    }

    public function destroy(Field $field)
    {
        if ($field->image) {
            Storage::disk('public')->delete($field->image);
        }
        $field->delete();
        return redirect()->route('admin.fields.index')->with('success', 'Lapangan berhasil dihapus!');
    }
}