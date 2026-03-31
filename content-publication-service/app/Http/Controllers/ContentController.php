<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Pengumuman; 
use App\Models\Renungan;
use App\Models\Galeri;

class ContentController extends Controller
{
    /**
     * Mengambil semua data pengumuman
     */
    public function getAllPengumuman()
    {
        // Mengambil data terbaru di atas
        $pengumuman = Pengumuman::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $pengumuman
        ]);
    }

    public function storePengumuman(\Illuminate\Http\Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'status' => 'required|string',
            'scope' => 'required|string'
        ]);

        // 2. Simpan Pengumuman ke Database Content
        $pengumuman = \App\Models\Pengumuman::create($request->all());

        // 3. LOGIKA INTER-SERVICE COMMUNICATION (KIRIM NOTIFIKASI)
        try {
            // Menentukan judul notifikasi berdasarkan scope (publik/jemaat vs rayon)
            $judulNotif = ($request->scope === 'rayon') 
                ? 'Pengumuman Baru Rayon' 
                : 'Pengumuman Baru Gereja';

            // Menembak API secara internal ke Administration & Utility Service (Port 8004)
            \Illuminate\Support\Facades\Http::post('http://127.0.0.1:8004/api/admin/notifikasi', [
                'judul' => $judulNotif,
                'isi' => 'Terdapat pengumuman baru: ' . $pengumuman->judul,
                'id_pengguna' => null, // Null = Broadcast ke target role (Jemaat & Pendeta)
                'jenis_referensi' => 'Pengumuman',
                'id_referensi' => $pengumuman->id
            ]);
            
        } catch (\Exception $e) {
            // Jika Administration Service sedang down/mati, Pengumuman tetap berhasil disimpan
            // Kita hanya mencatat errornya di log agar tidak merusak pengalaman pengguna
            \Illuminate\Support\Facades\Log::error('Gagal mengirim trigger notifikasi ke Port 8004: ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'data' => $pengumuman], 201);
    }

    /**
     * Mengubah data pengumuman
     */
    public function updatePengumuman(Request $request, $id)
    {
        $pengumuman = Pengumuman::find($id);
        
        if (!$pengumuman) {
            return response()->json(['status' => 'error', 'message' => 'Pengumuman tidak ditemukan'], 404);
        }

        // 1. Tambahkan validasi untuk 'scope' dan 'id_rayon'
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'status' => 'required|string',
            'scope' => 'required|in:publik,jemaat,rayon',
            'id_rayon' => 'nullable|integer'
        ]);

        // 2. Masukkan 'scope' dan 'id_rayon' ke dalam proses update
        $pengumuman->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'status' => $request->status,
            'scope' => $request->scope,
            // Jika scope diubah menjadi 'rayon', masukkan id_rayon. Jika tidak, kosongkan (null).
            'id_rayon' => $request->scope === 'rayon' ? $request->id_rayon : null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengumuman berhasil diperbarui',
            'data' => $pengumuman
        ]);
    }

    /**
     * Menghapus pengumuman
     */
    public function deletePengumuman($id)
    {
        $pengumuman = Pengumuman::find($id);
        
        if (!$pengumuman) {
            return response()->json(['status' => 'error', 'message' => 'Pengumuman tidak ditemukan'], 404);
        }

        $pengumuman->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengumuman berhasil dihapus'
        ]);
    }

    // ==========================================
    // MODULE: RENUNGAN HARIAN
    // ==========================================


    /**
     * UNTUK JEMAAT: Hanya mengambil renungan yang berstatus 'Aktif'
     */
    public function getRenunganJemaat()
    {
        $renungan = Renungan::where('status', 'Aktif')
            ->orderBy('published_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $renungan
        ]);
    }
    
    public function getAllRenungan()
    {
        $renungan = Renungan::orderBy('created_at', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $renungan]);
    }

    public function storeRenungan(\Illuminate\Http\Request $request)
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
            'id_penulis' => null, // Biarkan null jika middleware auth dimatikan sementara
            'published_at' => $request->status === 'Aktif' ? now() : null
        ]);

        return response()->json(['status' => 'success', 'data' => $renungan], 201);
    }

    public function updateRenungan(\Illuminate\Http\Request $request, $id)
    {
        $renungan = Renungan::find($id);
        if (!$renungan) return response()->json(['status' => 'error', 'message' => 'Renungan tidak ditemukan'], 404);

        $request->validate([
            'tema' => 'required|string|max:255',
            'ayat_pokok' => 'required|string|max:255',
            'isi' => 'required|string',
            'status' => 'required|string'
        ]);

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
        if (!$renungan) return response()->json(['status' => 'error', 'message' => 'Renungan tidak ditemukan'], 404);
        
        $renungan->delete();
        return response()->json(['status' => 'success', 'message' => 'Renungan dihapus']);
    }

    // ==========================================
    // MODULE: GALERI KEGIATAN
    // ==========================================

    public function getPublicGaleri()
    {
        // Rute publik untuk menarik data galeri di halaman Jemaat
        $galeri = Galeri::orderBy('tanggal_kegiatan', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $galeri]);
    }

    public function getAllGaleri()
    {
        $galeri = Galeri::orderBy('tanggal_kegiatan', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $galeri]);
    }

    public function storeGaleri(\Illuminate\Http\Request $request)
    {
        // 1. Validasi: 'foto' sekarang divalidasi sebagai string (teks Base64)
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tanggal_kegiatan' => 'required|date',
            'foto' => 'required|string' // Wajib berupa string Base64
        ]);

        $data = $request->except(['foto']);

        // 2. Dekode dan simpan Base64 menjadi file fisik
        if ($request->filled('foto') && preg_match('/^data:image\/(\w+);base64,/', $request->foto, $type)) {
            $data_without_prefix = substr($request->foto, strpos($request->foto, ',') + 1);
            $image_base64 = base64_decode($data_without_prefix);
            $extension = strtolower($type[1]); 
            $fileName = 'galeri/' . uniqid() . '.' . $extension;
            
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $data['path_foto'] = $fileName;
        } else {
            return response()->json(['status' => 'error', 'message' => 'Format foto tidak valid.'], 400);
        }

        $galeri = Galeri::create($data);
        return response()->json(['status' => 'success', 'data' => $galeri], 201);
    }

    public function updateGaleri(\Illuminate\Http\Request $request, $id)
    {
        $galeri = Galeri::find($id);
        if (!$galeri) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);

        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tanggal_kegiatan' => 'required|date',
            'foto' => 'nullable|string' // Opsional berupa string Base64
        ]);

        $dataUpdate = $request->except(['_method', 'foto']);

        // Jika ada foto baru (Base64) yang dikirim
        if ($request->filled('foto') && preg_match('/^data:image\/(\w+);base64,/', $request->foto, $type)) {
            
            // Hapus foto lama agar storage tidak penuh
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($galeri->path_foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($galeri->path_foto);
            }

            // Simpan foto baru
            $data_without_prefix = substr($request->foto, strpos($request->foto, ',') + 1);
            $image_base64 = base64_decode($data_without_prefix);
            $extension = strtolower($type[1]); 
            $fileName = 'galeri/' . uniqid() . '.' . $extension;
            
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
            $dataUpdate['path_foto'] = $fileName;
        }

        $galeri->update($dataUpdate);
        return response()->json(['status' => 'success', 'data' => $galeri]);
    }

    public function deleteGaleri($id)
    {
        $galeri = Galeri::find($id);
        if (!$galeri) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        
        // Hapus file fisik dari storage
        if (Storage::disk('public')->exists($galeri->path_foto)) {
            Storage::disk('public')->delete($galeri->path_foto);
        }

        $galeri->delete();
        return response()->json(['status' => 'success', 'message' => 'Galeri dan foto dihapus']);
    }
}