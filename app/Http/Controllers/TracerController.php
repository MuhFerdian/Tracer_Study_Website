<?php

namespace App\Http\Controllers;

use App\Models\alumniModel as Alumni;

class TracerController extends Controller
{
    public function statistikAlumni()
{
    $total = Alumni::count();

    $kerja = Alumni::whereRaw(
        "LOWER(status_pekerjaan) = ?",
        ['bekerja']
    )->count();

    $wirausaha = Alumni::whereRaw(
        "LOWER(status_pekerjaan) = ?",
        ['wirausaha']
    )->count();

    return response()->json([
        'status' => true,
        'data' => [
            'total' => $total,
            'kerja' => $kerja,
            'wirausaha' => $wirausaha,
        ]
    ]);
}
}