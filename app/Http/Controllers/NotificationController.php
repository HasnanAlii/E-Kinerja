<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Mengambil notifikasi 5 hari terakhir
    public function list()
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->where('waktu', '>=', now()->subDays(5))
            ->orderBy('waktu', 'desc')
            ->get();

        return response()->json($notifications);
    }

    // Tandai semua sebagai dibaca
    public function readAll()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
