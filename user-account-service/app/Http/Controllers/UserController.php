<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    // Mengambil semua data jemaat beserta profil dan rolenya
    public function getAllUsers()
    {
        $users = User::with(['profile', 'roles'])->get();
        
        // Memformat data agar mudah dibaca oleh React
        $formattedUsers = $users->map(function ($user) {
            return [
                'id'         => $user->id,
                'email'      => $user->email,
                'is_active'  => $user->is_active,
                'name'       => $user->profile ? $user->profile->full_name : 'Tanpa Nama',
                'role'       => $user->roles->first() ? $user->roles->first()->name : 'public',
                // KUNCI PENYELESAIAN: Kirimkan id_rayon ke React
                'id_rayon'   => $user->profile ? $user->profile->rayon_id : null,
                'created_at' => $user->created_at
            ];
        });

        return response()->json(['status' => 'success', 'data' => $formattedUsers]);
    }

    // Fungsi khusus untuk mengubah role user (Menyinkronkan Pivot Table)
    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        // Cari ID dari nama role yang dikirim React (misal: 'ketua_rayon')
        $role = Role::where('name', $request->role)->first();

        // Keajaiban terjadi di sini: sync() akan otomatis menghapus role lama 
        // di tabel user_roles dan menggantinya dengan role yang baru
        $user->roles()->sync([$role->id]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Role berhasil diperbarui menjadi ' . $role->name
        ]);
    }
}