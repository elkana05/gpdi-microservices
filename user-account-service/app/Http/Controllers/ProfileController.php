<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // <-- PASTIKAN INI DIIMPOR
use App\Models\User;

class ProfileController extends Controller
{

    public function updatePassword(Request $request)
    {
        $user = auth('api')->user();

        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            // Aturan 'confirmed' otomatis akan mengecek field 'new_password_confirmation'
            'new_password' => 'required|string|min:6|confirmed', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Validasi gagal, pastikan konfirmasi password cocok.', 
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Cek apakah password lama yang dimasukkan sesuai dengan yang ada di database
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Password lama yang Anda masukkan salah.'
            ], 400); // 400 Bad Request
        }

        // 3. Simpan password baru
        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success', 
            'message' => 'Password berhasil diperbarui.', 
            'data' => null, 
            'meta' => null
        ], 200);
    }


    public function show()
    {
        $profile = auth('api')->user()->profile;
        return response()->json([
            'status' => 'success', 'message' => 'Data retrieved successfully',
            'data' => $profile ?? (object)[], 'meta' => null
        ], 200);
    }

    public function update(Request $request)
    {
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'rayon_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $validator->validated()
        );

        return response()->json(['status' => 'success', 'message' => 'Profile updated successfully', 'data' => $profile, 'meta' => null], 200);
    }

    /**
     * Mengambil seluruh data pengguna beserta profilnya
     */
    public function getAllJemaat()
    {
        // PERBAIKAN: Menambahkan 'profiles.rayon_id as id_rayon' agar React bisa membacanya
        $users = User::leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->select('users.id', 'users.email', 'profiles.full_name as name', 'profiles.rayon_id as id_rayon', 'users.created_at')
            ->orderBy('users.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Menghapus data jemaat
     */
    public function deleteJemaat($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Data jemaat tidak ditemukan'], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data jemaat berhasil dihapus'
        ]);
    }

    /**
     * Menambahkan akun jemaat baru ke tabel users dan profiles
     */
    public function storeJemaat(Request $request)
    {
        // PERBAIKAN: Tambahkan validasi id_rayon
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'id_rayon' => 'nullable|integer' 
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan ke tabel users
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            // 2. Simpan nama DAN RAYON ke tabel profiles
            DB::table('profiles')->insert([
                'user_id' => $user->id,
                'full_name' => $request->name, 
                'rayon_id' => $request->id_rayon, // PERBAIKAN UTAMA
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Akun jemaat dan profil berhasil dibuat'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengubah data email di tabel users dan nama di tabel profiles
     */
    public function updateJemaat(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        // PERBAIKAN: Tambahkan validasi id_rayon
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'id_rayon' => 'nullable|integer'
        ]);

        DB::beginTransaction();
        try {
            // 1. Update tabel users
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // 2. Update tabel profiles DAN RAYON
            DB::table('profiles')->updateOrInsert(
                ['user_id' => $id],
                [
                    'full_name' => $request->name, 
                    'rayon_id' => $request->id_rayon, // PERBAIKAN UTAMA
                    'updated_at' => now()
                ]
            );

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data jemaat berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }
}