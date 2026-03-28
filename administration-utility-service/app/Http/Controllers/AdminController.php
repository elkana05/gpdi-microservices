<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PilihanSurat;
use App\Models\Notifikasi;

class AdminController extends Controller
{
    // --- FITUR REQUEST SURAT ---
    public function getLetterOptions(Request $request)
    {
        // Semua user yang login (kecuali public) bisa melihat opsi surat
        $options = PilihanSurat::where('is_active', true)->get();
        return response()->json([
            'status' => 'success', 'message' => 'Letter options retrieved successfully',
            'data' => $options, 'meta' => null
        ], 200);
    }

    // --- FITUR NOTIFIKASI ---
    public function getNotifications(Request $request)
    {
        $user = $request->auth_user;
        
        // Ambil notifikasi khusus user tersebut ATAU notifikasi broadcast (id_pengguna = null)
        $notifications = Notifikasi::where('id_pengguna', $user['id'])
            ->orWhereNull('id_pengguna')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success', 'message' => 'Notifications retrieved',
            'data' => $notifications, 'meta' => null
        ], 200);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->auth_user;
        $notif = Notifikasi::where('id', $id)->where('id_pengguna', $user['id'])->first();

        if (!$notif) {
            return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);
        }

        $notif->update(['is_read' => true]);

        return response()->json([
            'status' => 'success', 'message' => 'Notification marked as read',
            'data' => $notif, 'meta' => null
        ], 200);
    }
}