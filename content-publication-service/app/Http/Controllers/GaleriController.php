<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    // GET /api/galeri — publik
    public function index()
    {
        $galeri = Galeri::orderByDesc('tanggal_kegiatan')->paginate(12);
        $galeri->getCollection()->transform(fn($item) => array_merge(
            $item->toArray(),
            ['foto_url' => asset('storage/' . $item->foto)]
        ));
        return response()->json($galeri);
    }

    // GET /api/galeri/{id}
    public function show($id)
    {
        $item = Galeri::findOrFail($id);
        return response()->json(array_merge($item->toArray(), ['foto_url' => asset('storage/' . $item->foto)]));
    }

    // POST /api/galeri — hanya pendeta
    public function store(Request $request)
    {
        $request->validate([
            'judul'             => 'required|string|max:255',
            'deskripsi'         => 'nullable|string',
            'foto'              => 'required|image|max:4096',
            'tanggal_kegiatan'  => 'required|date',
        ]);

        $path = $request->file('foto')->store('galeri', 'public');
        $galeri = Galeri::create([
            ...$request->except('foto'),
            'foto'        => $path,
            'dibuat_oleh' => $request->header('X-User-Id', 0),
        ]);

        return response()->json(['message' => 'Foto galeri berhasil ditambahkan', 'data' => $galeri], 201);
    }

    // DELETE /api/galeri/{id} — hanya pendeta
    public function destroy($id)
    {
        $item = Galeri::findOrFail($id);
        Storage::delete('public/' . $item->foto);
        $item->delete();
        return response()->json(['message' => 'Foto galeri berhasil dihapus']);
    }
}
