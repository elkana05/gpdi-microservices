<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilGereja;
use App\Models\Pelayanan;
use App\Models\Galeri;
use App\Models\Pengumuman;

class PublicController extends Controller
{
    public function homepage()
    {
        // Mengembalikan data esensial untuk beranda (bisa dikembangkan sesuai kebutuhan front-end)
        $profil = ProfilGereja::select('nama_gereja', 'ayat_tahunan', 'banner_beranda')->first();
        return response()->json([
            'status' => 'success', 'message' => 'Homepage data retrieved',
            'data' => $profil ?? (object)[], 'meta' => null
        ], 200);
    }

    public function churchProfile()
    {
        $profil = ProfilGereja::first();
        return response()->json([
            'status' => 'success', 'message' => 'Church profile retrieved',
            'data' => $profil ?? (object)[], 'meta' => null
        ], 200);
    }

    public function serviceInformation()
    {
        $pelayanan = Pelayanan::all();
        return response()->json([
            'status' => 'success', 'message' => 'Service information retrieved',
            'data' => $pelayanan, 'meta' => null
        ], 200);
    }

    public function galleries()
    {
        $galeri = Galeri::orderBy('tanggal_kegiatan', 'desc')->get();
        return response()->json([
            'status' => 'success', 'message' => 'Galleries retrieved',
            'data' => $galeri, 'meta' => null
        ], 200);
    }

    public function contactLocation()
    {
        $profil = ProfilGereja::select('alamat', 'nomor_kontak', 'link_maps', 'link_sosmed')->first();
        return response()->json([
            'status' => 'success', 'message' => 'Contact and location retrieved',
            'data' => $profil ?? (object)[], 'meta' => null
        ], 200);
    }

    /**
     * Pengumuman publik — untuk halaman yang bisa diakses tanpa login.
     * Hanya menampilkan pengumuman berstatus 'Aktif' dengan scope 'publik'.
     */
    public function announcements()
    {
        $pengumuman = Pengumuman::where('scope', 'publik')
            ->where('status', 'Aktif')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success', 'message' => 'Public announcements retrieved',
            'data' => $pengumuman, 'meta' => null
        ], 200);
    }

    /**
     * Pengumuman untuk jemaat (anggota yang sudah login).
     * Menampilkan pengumuman berstatus 'Aktif' dengan scope 'publik' ATAU 'jemaat'.
     * Pengumuman scope 'rayon' TIDAK ditampilkan di sini (ada halaman terpisah).
     */
    public function getJemaatPengumuman()
    {
        $pengumuman = Pengumuman::whereIn('scope', ['publik', 'jemaat'])
            ->where('status', 'Aktif')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success', 'message' => 'Jemaat announcements retrieved',
            'data' => $pengumuman, 'meta' => null
        ], 200);
    }

    /**
     * Pengumuman khusus Rayon — hanya untuk anggota rayon yang bersangkutan.
     * Membaca id_rayon dari JWT claims yang sudah di-inject oleh JwtMiddleware.
     */
    public function getRayonPengumuman(Request $request)
    {
        $authUser = $request->input('auth_user');
        $idRayon  = $authUser['id_rayon'] ?? null;

        if (!$idRayon) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda belum terdaftar di Rayon mana pun.',
                'data'    => []
            ], 200); // 200 agar FE tidak error, data kosong saja
        }

        $pengumuman = Pengumuman::where('scope', 'rayon')
            ->where('id_rayon', $idRayon)
            ->where('status', 'Aktif')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success', 'message' => 'Rayon announcements retrieved',
            'data'   => $pengumuman, 'meta' => null
        ], 200);
    }

    public function showAnnouncement($id)
    {
        $pengumuman = Pengumuman::where('scope', 'publik')
            ->where('status', 'Aktif')
            ->find($id);

        if (!$pengumuman) {
            return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);
        }

        return response()->json([
            'status' => 'success', 'message' => 'Announcement retrieved',
            'data' => $pengumuman, 'meta' => null
        ], 200);
    }
}