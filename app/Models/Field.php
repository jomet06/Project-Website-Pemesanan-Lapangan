<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_fields';

    protected $fillable = [
        'name_fields',
        'type_fields',
        'description',
        'price_per_hour',
        'capacity',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_per_hour' => 'decimal:2',
    ];

    // Relationships
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'field_id', 'id_fields');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'field_facility', 'field_id', 'facility_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}