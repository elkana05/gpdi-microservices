<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

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
        // Log request untuk debugging
        Log::info('Update Role Request', [
            'user_id' => $id,
            'request_body' => $request->all(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method()
        ]);

        // Validasi input dengan custom messages
        try {
            $validated = $request->validate([
                'role' => 'required|string|exists:roles,name'
            ], [
                'role.required' => 'Field role harus diisi',
                'role.string' => 'Field role harus berupa string',
                'role.exists' => 'Role "' . $request->role . '" tidak terdaftar. Gunakan: pendeta, jemaat_aktif, atau ketua_rayon'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation Error', [
                'user_id' => $id,
                'errors' => $e->errors()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        // Cari ID dari nama role yang dikirim React (misal: 'ketua_rayon')
        $role = Role::where('name', $validated['role'])->first();
        
        // Pastikan role ditemukan
        if (!$role) {
            Log::error('Role not found', [
                'requested_role' => $validated['role'],
                'available_roles' => Role::pluck('name')->toArray()
            ]);
            return response()->json([
                'status' => 'error', 
                'message' => 'Role "' . $validated['role'] . '" tidak ditemukan dalam database'
            ], 404);
        }

        try {
            // Keajaiban terjadi di sini: sync() akan otomatis menghapus role lama 
            // di tabel user_roles dan menggantinya dengan role yang baru
            $user->roles()->sync([$role->id]);

            Log::info('Role updated successfully', [
                'user_id' => $user->id,
                'new_role' => $role->name
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'Role berhasil diperbarui menjadi ' . $role->name,
                'data' => [
                    'user_id' => $user->id,
                    'role' => $role->name
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Role update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate role: ' . $e->getMessage()
            ], 500);
        }
    }
}