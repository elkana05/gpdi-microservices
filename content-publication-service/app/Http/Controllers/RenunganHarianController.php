<?php

namespace App\Http\Controllers;

use App\Models\RenunganHarian;
use Illuminate\Http\Request;

class RenunganHarianController extends Controller
{
    // GET /api/renungan — jemaat terdaftar
    public function index()
    {
        $data = RenunganHarian::published()->orderByDesc('tanggal_publikasi')->paginate(10);
        return response()->json($data);
    }

    // GET /api/renungan/hari-ini — renungan hari ini
    public function hariIni()
    {
        $renungan = RenunganHarian::published()->hariIni()->first();
        if (!$renungan) {
            return response()->json(['message' => 'Belum ada renungan untuk hari ini'], 404);
        }
        return response()->json($renungan);
    }

    // GET /api/renungan/{id}
    public function show($id)
    {
        return response()->json(RenunganHarian::findOrFail($id));
    }

    // GET /api/renungan/semua — pendeta (semua termasuk draft)
    public function indexAll()
    {
        return response()->json(RenunganHarian::orderByDesc('tanggal_publikasi')->paginate(15));
    }

    // POST /api/renungan — hanya pendeta
    public function store(Request $request)
    {
        $request->validate([
            'judul'              => 'required|string|max:255',
            'ayat_alkitab'       => 'required|string|max:255',
            'isi_renungan'       => 'required|string',
            'tanggal_publikasi'  => 'required|date',
            'status'             => 'sometimes|in:draft,published',
        ]);

        $renungan = RenunganHarian::create([
            ...$request->all(),
            'dibuat_oleh' => $request->header('X-User-Id', 0),
        ]);
        return response()->json(['message' => 'Renungan berhasil dibuat', 'data' => $renungan], 201);
    }

    // PUT /api/renungan/{id}
    public function update(Request $request, $id)
    {
        $renungan = RenunganHarian::findOrFail($id);
        $request->validate([
            'judul'             => 'sometimes|string|max:255',
            'ayat_alkitab'      => 'sometimes|string|max:255',
            'isi_renungan'      => 'sometimes|string',
            'tanggal_publikasi' => 'sometimes|date',
            'status'            => 'sometimes|in:draft,published',
        ]);
        $renungan->update($request->all());
        return response()->json(['message' => 'Renungan berhasil diperbarui', 'data' => $renungan]);
    }

    // DELETE /api/renungan/{id}
    public function destroy($id)
    {
        RenunganHarian::findOrFail($id)->delete();
        return response()->json(['message' => 'Renungan berhasil dihapus']);
    }
}
