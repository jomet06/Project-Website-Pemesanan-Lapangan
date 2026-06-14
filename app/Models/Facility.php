<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_facilities';

    protected $fillable = [
        'name_facilities',
        'icon',
    ];

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'field_facility', 'facility_id', 'field_id');
    }
}