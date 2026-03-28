<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    // GET /api/jadwal — publik
    public function index(Request $request)
    {
        $query = Jadwal::aktif();
        if ($request->jenis) $query->where('jenis', $request->jenis);
        if ($request->hari)  $query->where('hari', $request->hari);
        return response()->json($query->orderBy('hari')->orderBy('waktu_mulai')->get());
    }

    // GET /api/jadwal/{id}
    public function show($id)
    {
        return response()->json(Jadwal::findOrFail($id));
    }

    // POST /api/jadwal — hanya pendeta
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis'         => 'required|in:ibadah_umum,ibadah_rayon,kegiatan_khusus,lainnya',
            'hari'          => 'required|string',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'lokasi'        => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        $jadwal = Jadwal::create($request->all());
        return response()->json(['message' => 'Jadwal berhasil ditambahkan', 'data' => $jadwal], 201);
    }

    // PUT /api/jadwal/{id} — hanya pendeta
    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $request->validate([
            'nama_kegiatan' => 'sometimes|string|max:255',
            'jenis'         => 'sometimes|in:ibadah_umum,ibadah_rayon,kegiatan_khusus,lainnya',
            'hari'          => 'sometimes|string',
            'waktu_mulai'   => 'sometimes|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'lokasi'        => 'nullable|string',
            'keterangan'    => 'nullable|string',
            'is_aktif'      => 'sometimes|boolean',
        ]);
        $jadwal->update($request->all());
        return response()->json(['message' => 'Jadwal berhasil diperbarui', 'data' => $jadwal]);
    }

    // DELETE /api/jadwal/{id} — hanya pendeta
    public function destroy($id)
    {
        Jadwal::findOrFail($id)->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus']);
    }
}
