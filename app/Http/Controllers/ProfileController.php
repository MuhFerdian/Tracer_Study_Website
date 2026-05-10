<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\alumniModel as Alumni;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user_id = $request->query('user_id');

        $alumni = Alumni::where('user_id', $user_id)->first();

        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'debug_user_id' => $user_id
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $alumni
        ]);
    }
}