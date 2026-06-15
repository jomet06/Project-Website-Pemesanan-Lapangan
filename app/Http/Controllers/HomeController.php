<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $fields = Field::with('facilities')->latest()->take(3)->get();
        return view('layouts.welcome', compact('fields'));
    }
}