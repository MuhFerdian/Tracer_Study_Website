<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationHistoryController extends Controller
{
    public function index()
    {
        return response()->json(
            Notification::latest()->get()
        );
    }
}
