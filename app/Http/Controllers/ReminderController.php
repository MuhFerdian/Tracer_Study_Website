<?php 
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Answer;
use App\Services\FcmService;

class ReminderController extends Controller
{
    public function kirimReminder(FcmService $fcm)
    {
        $users = User::with('alumni')->get();

        foreach ($users as $user) {

            if (!$user->alumni) continue;

            $sudahIsi = Answer::where('alumni_id', $user->alumni->id)->exists();

            if (!$sudahIsi && $user->fcm_token) {

                $fcm->sendToUser(
                    $user->id,
                    "Reminder Survey 📢",
                    "Kamu belum mengisi survey tracer study"
                );
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Reminder berhasil dikirim'
        ]);
    }
}