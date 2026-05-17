<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FcmService;
use App\Models\Notification;
use App\Models\Answer;
use App\Models\alumniModel;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Kirim notifikasi FCM (existing)
     */
    public function send(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $notif = Notification::create([
            'title' => 'Info Tracer Study',
            'body'  => 'Data kamu berhasil diperbarui!',
            'type'  => 'info'
        ]);

        $fcm    = new FcmService();
        $result = $fcm->sendNotification($request->token, $notif->title, $notif->body);

        return response()->json(['db' => $notif, 'fcm' => $result]);
    }

    /**
     * Ambil daftar alumni yang baru mengisi survey (untuk dropdown notifikasi admin)
     * Diurutkan dari yang paling baru, limit 15
     */
    public function getAlumniMengisi(Request $request)
    {
        // Ambil 1 baris per alumni_id (yang paling baru)
        $data = Answer::select(
                'answers.alumni_id',
                DB::raw('MAX(answers.created_at) as submitted_at')
            )
            ->groupBy('answers.alumni_id')
            ->orderByDesc('submitted_at')
            ->limit(15)
            ->get();

        $result = $data->map(function ($row) {
            $alumni = alumniModel::find($row->alumni_id);
            return [
                'alumni_id'     => $row->alumni_id,
                'nama'          => $alumni->nama  ?? 'Alumni #' . $row->alumni_id,
                'nim'           => $alumni->nim   ?? '-',
                'prodi'         => $alumni->prodi ?? '-',
                'submitted_at'  => $row->submitted_at,
                'submitted_ago' => \Carbon\Carbon::parse($row->submitted_at)->diffForHumans(),
            ];
        });

        // Badge = jumlah alumni unik yang sudah mengisi (COUNT DISTINCT yang benar)
        $unread = DB::table('answers')
            ->select(DB::raw('COUNT(DISTINCT alumni_id) as total'))
            ->value('total');

        return response()->json([
            'data'   => $result,
            'unread' => (int) $unread,
        ]);
    }

    /**
     * Jumlah alumni unik yang sudah mengisi survey (badge count)
     */
    public function unreadCount()
    {
        $count = DB::table('answers')
            ->select(DB::raw('COUNT(DISTINCT alumni_id) as total'))
            ->value('total');

        return response()->json(['count' => (int) $count]);
    }

    /**
     * Mark single notification as read (placeholder, bisa dikembangkan)
     */
    public function markRead($id)
    {
        Notification::where('id', $id)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * Mark all as read (placeholder)
     */
    public function markAllRead()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
