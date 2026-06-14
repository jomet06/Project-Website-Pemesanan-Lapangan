<?php

use Illuminate\Support\Facades\Route;
use App\Models\Field;

// Landing Page Route
Route::get('/', function () {
    // Mengambil 3 lapangan yang aktif secara acak atau terbaru untuk ditampilkan di beranda
    $fields = Field::where('is_active', true)->latest()->take(3)->get();
    
    return view('layouts.welcome', compact('fields'));
})->name('home');

// Sisanya biarkan rute-rute yang sudah kamu buat sebelumnya...