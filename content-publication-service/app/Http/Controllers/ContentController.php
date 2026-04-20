<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Pengumuman;
use App\Models\Renungan;
use App\Models\Galeri;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller
{
    /**
     * Mengambil semua data pengumuman
     */
    public function getAllPengumuman()
    {
        $pengumuman = Pengumuman::orderBy('created_at', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $pengumuman]);
    }

    public function storePengumuman(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'status' => 'required|string',
            'scope' => 'required|string'
        ]);

        $pengumuman = Pengumuman::create($request->all());

        // FIX: Gunakan nama service docker dan tambahkan timeout
        try {
            $judulNotif = ($request->scope === 'rayon') ? 'Pengumuman Baru Rayon' : 'Pengumuman Baru Gereja';

            // Gunakan URL Internal Docker
            Http::timeout(3)->post('http://administration-utility-service:8004/api/admin/notifikasi', [
                'judul' => $judulNotif,
                'isi' => 'Terdapat pengumuman baru: ' . $pengumuman->judul,
                'id_pengguna' => null,
                'jenis_referensi' => 'Pengumuman',
                'id_referensi' => $pengumuman->id
            ]);

        } catch (\Exception $e) {
            Log::error('Notifikasi Gagal: ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $pengumuman], 201);
    }

    public function updatePengumuman(Request $request, $id)
    {
        $pengumuman = Pengumuman::find($id);
        if (!$pengumuman) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'status' => 'required|string',
            'scope' => 'required|in:publik,jemaat,rayon',
            'id_rayon' => 'nullable|integer'
        ]);

        $pengumuman->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'status' => $request->status,
            'scope' => $request->scope,
            'id_rayon' => $request->scope === 'rayon' ? $request->id_rayon : null,
        ]);

        return response()->json(['status' => 'success', 'data' => $pengumuman]);
    }

    public function deletePengumuman($id)
    {
        $pengumuman = Pengumuman::find($id);
        if (!$pengumuman) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        $pengumuman->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }

    // RENUNGAN
    public function getRenunganJemaat()
    {
        $renungan = Renungan::where('status', 'Aktif')->orderBy('published_at', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $renungan]);
    }

    public function getAllRenungan()
    {
        $renungan = Renungan::orderBy('created_at', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $renungan]);
    }

    public function storeRenungan(Request $request)
    {
        $request->validate([
            'tema' => 'required|string|max:255',
            'ayat_pokok' => 'required|string|max:255',
            'isi' => 'required|string',
            'status' => 'required|string'
        ]);

        $renungan = Renungan::create([
            'tema' => $request->tema,
            'ayat_pokok' => $request->ayat_pokok,
            'isi' => $request->isi,
            'status' => $request->status,
            'id_penulis' => null,
            'published_at' => $request->status === 'Aktif' ? now() : null
        ]);

        return response()->json(['status' => 'success', 'data' => $renungan], 201);
    }

    public function updateRenungan(Request $request, $id)
    {
        $renungan = Renungan::find($id);
        if (!$renungan) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);

        $request->validate(['tema' => 'required', 'ayat_pokok' => 'required', 'isi' => 'required', 'status' => 'required']);

        $renungan->update([
            'tema' => $request->tema,
            'ayat_pokok' => $request->ayat_pokok,
            'isi' => $request->isi,
            'status' => $request->status,
            'published_at' => $request->status === 'Aktif' ? now() : null
        ]);

        return response()->json(['status' => 'success', 'data' => $renungan]);
    }

    public function deleteRenungan($id)
    {
        $renungan = Renungan::find($id);
        if ($renungan) $renungan->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }

    // GALERI
    public function getPublicGaleri()
    {
        $galeri = Galeri::orderBy('tanggal_kegiatan', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $galeri]);
    }

    public function getAllGaleri()
    {
        $galeri = Galeri::orderBy('tanggal_kegiatan', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $galeri]);
    }

    public function storeGaleri(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'kategori' => 'required',
            'tanggal_kegiatan' => 'required|date',
            'foto' => 'required|string'
        ]);

        $data = $request->except(['foto']);

        if ($request->filled('foto') && preg_match('/^data:image\/(\w+);base64,/', $request->foto, $type)) {
            $image_base64 = base64_decode(substr($request->foto, strpos($request->foto, ',') + 1));
            $fileName = 'galeri/' . uniqid() . '.' . strtolower($type[1]);
            Storage::disk('public')->put($fileName, $image_base64);
            $data['path_foto'] = $fileName;
        }

        $galeri = Galeri::create($data);
        return response()->json(['status' => 'success', 'data' => $galeri], 201);
    }

    public function updateGaleri(Request $request, $id)
    {
        $galeri = Galeri::find($id);
        if (!$galeri) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);

        $dataUpdate = $request->except(['foto']);

        if ($request->filled('foto') && preg_match('/^data:image\/(\w+);base64,/', $request->foto, $type)) {
            if ($galeri->path_foto) Storage::disk('public')->delete($galeri->path_foto);
            $image_base64 = base64_decode(substr($request->foto, strpos($request->foto, ',') + 1));
            $fileName = 'galeri/' . uniqid() . '.' . strtolower($type[1]);
            Storage::disk('public')->put($fileName, $image_base64);
            $dataUpdate['path_foto'] = $fileName;
        }

        $galeri->update($dataUpdate);
        return response()->json(['status' => 'success', 'data' => $galeri]);
    }

    public function deleteGaleri($id)
    {
        $galeri = Galeri::find($id);
        if ($galeri) {
            Storage::disk('public')->delete($galeri->path_foto);
            $galeri->delete();
        }
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}
