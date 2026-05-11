<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FamilyMemberController extends Controller
{
    /**
     * PERBAIKAN: Mengizinkan role 'jemaat' atau 'ketua_rayon' untuk mengelola keluarga.
     */
    private function checkRole() {
        $user = auth('api')->user();
        if (!$user) return false;

        $role = $user->roles()->first();
        // Sekarang kita izinkan 'jemaat' (nama baru) dan 'ketua_rayon'
        return $role && in_array($role->name, ['jemaat', 'ketua_rayon', 'pendeta', 'admin']);
    }

    public function index()
    {
        $members = auth('api')->user()->familyMembers;
        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully', 'data' => $members, 'meta' => null], 200);
    }

    public function store(Request $request)
    {
        if (!$this->checkRole()) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk menambah anggota keluarga.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'relationship' => 'required|string',
            'gender' => 'required|string',
            'birth_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $member = auth('api')->user()->familyMembers()->create($validator->validated());
        return response()->json(['status' => 'success', 'message' => 'Anggota keluarga berhasil ditambahkan', 'data' => $member, 'meta' => null], 201);
    }

    public function update(Request $request, $id)
    {
        if (!$this->checkRole()) {
            return response()->json(['status' => 'error', 'message' => 'Izin ditolak'], 403);
        }

        $member = auth('api')->user()->familyMembers()->find($id);
        if (!$member) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|required|string',
            'relationship' => 'sometimes|required|string',
            'gender' => 'sometimes|required|string',
            'birth_date' => 'sometimes|required|date'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);

        $member->update($validator->validated());
        return response()->json(['status' => 'success', 'message' => 'Data berhasil diubah', 'data' => $member, 'meta' => null], 200);
    }

    public function destroy($id)
    {
        if (!$this->checkRole()) return response()->json(['status' => 'error', 'message' => 'Izin ditolak'], 403);

        $member = auth('api')->user()->familyMembers()->find($id);
        if (!$member) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);

        $member->delete();
        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus'], 200);
    }
}
