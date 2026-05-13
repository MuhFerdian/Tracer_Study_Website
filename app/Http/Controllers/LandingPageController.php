<?php

namespace App\Http\Controllers;

use App\Models\LowonganPekerjaan;

class LandingPageController extends Controller
{
    public function index()
    {
        $lowongan = LowonganPekerjaan::where('aktif', true)
                        ->latest()
                        ->take(3)
                        ->get();

        return view('layoutLandingPage.hero', compact('lowongan'));
    }
}