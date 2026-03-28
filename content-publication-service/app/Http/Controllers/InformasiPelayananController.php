<?php

namespace App\Http\Controllers;

use App\Models\InformasiPelayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InformasiPelayananController extends Controller
{
    // GET /api/informasi-pelayanan — publik
    public function index()
    {
        return response()->json(InformasiPelayanan::aktif()->get());
    }

    // GET /api/informasi-pelayanan/{id}
    public function show($id)
    {
        return response()->json(InformasiPelayanan::findOrFail($id));
    }

    // POST /api/informasi-pelayanan — hanya pendeta
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelayanan'    => 'required|string|max:255',
            'deskripsi'         => 'required|string',
            'target_usia'       => 'nullable|string',
            'jadwal'            => 'nullable|string',
            'penanggung_jawab'  => 'nullable|string',
            'foto'              => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('pelayanan', 'public');
        }

        $pelayanan = InformasiPelayanan::create($data);
        return response()->json(['message' => 'Informasi pelayanan berhasil ditambahkan', 'data' => $pelayanan], 201);
    }

    // PUT /api/informasi-pelayanan/{id} — hanya pendeta
    public function update(Request $request, $id)
    {
        $pelayanan = InformasiPelayanan::findOrFail($id);
        $request->validate([
            'nama_pelayanan' => 'sometimes|string|max:255',
            'deskripsi'      => 'sometimes|string',
            'is_aktif'       => 'sometimes|boolean',
            'foto'           => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        if ($request->hasFile('foto')) {
            if ($pelayanan->foto) Storage::delete('public/' . $pelayanan->foto);
            $data['foto'] = $request->file('foto')->store('pelayanan', 'public');
        }

        $pelayanan->update($data);
        return response()->json(['message' => 'Informasi pelayanan berhasil diperbarui', 'data' => $pelayanan]);
    }

    // DELETE /api/informasi-pelayanan/{id} — hanya pendeta
    public function destroy($id)
    {
        $item = InformasiPelayanan::findOrFail($id);
        if ($item->foto) Storage::delete('public/' . $item->foto);
        $item->delete();
        return response()->json(['message' => 'Informasi pelayanan berhasil dihapus']);
    }
}
