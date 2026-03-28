<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Rayon;
use App\Models\AnggotaRayon;

class RayonController extends Controller
{
    // 1. Mengambil semua data Rayon (Bisa diakses user yang login)
    public function index(Request $request)
    {
        $rayons = Rayon::all();
        return response()->json([
            'status' => 'success', 'message' => 'Data Rayon berhasil diambil',
            'data' => $rayons, 'meta' => null
        ], 200);
    }

    // 2. Membuat Rayon baru (Otoritas: Hanya Pendeta)
    public function store(Request $request)
    {
        $user = $request->auth_user;
        if ($user['role'] !== 'pendeta') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak. Hanya Pendeta yang dapat membuat Rayon.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_rayon' => 'required|string|max:100',
            'id_ketua_rayon' => 'nullable|uuid', // UUID User dari User Service
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);

        $rayon = Rayon::create($validator->validated());

        return response()->json([
            'status' => 'success', 'message' => 'Rayon berhasil dibuat',
            'data' => $rayon, 'meta' => null
        ], 201);
    }

    // 3. Melihat detail Rayon beserta daftar anggotanya
    public function show($id)
    {
        $rayon = Rayon::find($id);
        if (!$rayon) return response()->json(['status' => 'error', 'message' => 'Rayon tidak ditemukan'], 404);

        $rayon->anggota = AnggotaRayon::where('id_rayon', $id)->get();

        return response()->json([
            'status' => 'success', 'message' => 'Detail Rayon berhasil diambil',
            'data' => $rayon, 'meta' => null
        ], 200);
    }

    // 4. Mengupdate data Rayon (Otoritas: Hanya Pendeta)
    public function update(Request $request, $id)
    {
        $user = $request->auth_user;
        if ($user['role'] !== 'pendeta') return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);

        $rayon = Rayon::find($id);
        if (!$rayon) return response()->json(['status' => 'error', 'message' => 'Rayon tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'nama_rayon' => 'sometimes|required|string|max:100',
            'id_ketua_rayon' => 'nullable|uuid',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);

        $rayon->update($validator->validated());

        return response()->json([
            'status' => 'success', 'message' => 'Rayon berhasil diupdate',
            'data' => $rayon, 'meta' => null
        ], 200);
    }

    // 5. Menambahkan Jemaat ke Rayon (Otoritas: Pendeta & Ketua Rayon terkait)
    public function addMember(Request $request, $id)
    {
        $user = $request->auth_user;
        $rayon = Rayon::find($id);
        if (!$rayon) return response()->json(['status' => 'error', 'message' => 'Rayon tidak ditemukan'], 404);

        if ($user['role'] !== 'pendeta' && ($user['role'] !== 'ketua_rayon' || $rayon->id_ketua_rayon !== $user['id'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak. Anda tidak berwenang mengelola rayon ini.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id_jemaat' => 'required|uuid',
            'tanggal_bergabung' => 'nullable|date'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);

        $exists = AnggotaRayon::where('id_rayon', $id)->where('id_jemaat', $request->id_jemaat)->first();
        if ($exists) return response()->json(['status' => 'error', 'message' => 'Jemaat sudah terdaftar di Rayon ini'], 400);

        $anggota = AnggotaRayon::create([
            'id_rayon' => $id,
            'id_jemaat' => $request->id_jemaat,
            'tanggal_bergabung' => $request->tanggal_bergabung ?? now()
        ]);

        return response()->json([
            'status' => 'success', 'message' => 'Jemaat berhasil ditambahkan ke Rayon',
            'data' => $anggota, 'meta' => null
        ], 201);
    }
}