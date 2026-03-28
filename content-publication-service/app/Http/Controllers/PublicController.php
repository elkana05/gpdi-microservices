<?php

namespace App\Http\Controllers;

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

    public function announcements()
    {
        // Publik hanya melihat pengumuman yang di-set public dan published
        $pengumuman = Pengumuman::where('scope', 'public')
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success', 'message' => 'Public announcements retrieved',
            'data' => $pengumuman, 'meta' => null
        ], 200);
    }

    public function showAnnouncement($id)
    {
        $pengumuman = Pengumuman::where('scope', 'public')
            ->where('status', 'published')
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