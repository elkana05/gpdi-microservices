<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class ProfileController extends Controller
{
    // ... (fungsi show, update, dll tetap sama)

    public function updatePassword(Request $request)
    {
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Password lama salah.'], 400);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diperbarui.'], 200);
    }

    public function show()
    {
        $user    = auth('api')->user();
        $profile = $user->profile;

        return response()->json([
            'status' => 'success',
            'data'   => array_merge(
                $profile ? $profile->toArray() : [],
                ['email' => $user->email]   // tambahkan email dari tabel users
            )
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
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $profile = $user->profile()->updateOrCreate(['user_id' => $user->id], $validator->validated());
        return response()->json(['status' => 'success', 'data' => $profile], 200);
    }

    public function getAllJemaat()
    {
        // Hanya admin atau pendeta yang boleh melihat semua data user
        $currentUser = auth('api')->user();
        $role = $currentUser?->roles()->first()?->name;
        if (!in_array($role, ['admin', 'pendeta'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $users = User::with(['profile', 'roles'])->orderBy('created_at', 'desc')->get();
        $formatted = $users->map(function($user) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->profile->full_name ?? 'Tanpa Nama',
                'id_rayon' => $user->profile->rayon_id ?? null,
                'role' => $user->roles->first()->name ?? 'jemaat',
                'created_at' => $user->created_at
            ];
        });
        return response()->json(['status' => 'success', 'data' => $formatted]);
    }

    public function storeJemaat(Request $request)
    {
        // Keep the frontend role name compatible with the persisted database role.
        if ($request->input('role') === 'jemaat_aktif') {
            $request->merge(['role' => 'jemaat']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|exists:roles,name',
            'id_rayon' => 'nullable|integer'
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $role = Role::where('name', $request->role)->first();
            $user->roles()->attach($role->id);

            DB::table('profiles')->insert([
                'user_id' => $user->id,
                'full_name' => $request->name,
                'rayon_id' => $request->id_rayon,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Akun berhasil dibuat dengan peran ' . $request->role], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateJemaat(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'id_rayon' => 'nullable|integer'
        ]);

        DB::beginTransaction();
        try {
            $user->email = $request->email;
            if ($request->filled('password')) $user->password = bcrypt($request->password);
            $user->save();

            DB::table('profiles')->updateOrInsert(
                ['user_id' => $id],
                ['full_name' => $request->name, 'rayon_id' => $request->id_rayon, 'updated_at' => now()]
            );

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data jemaat berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteJemaat($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
