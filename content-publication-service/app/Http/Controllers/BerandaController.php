<?php

namespace App\Http\Controllers;

use App\Models\ProfilGereja;
use App\Models\Jadwal;
use App\Models\Pengumuman;
use App\Models\RenunganHarian;
use App\Models\Galeri;

class BerandaController extends Controller
{
    // GET /api/beranda — publik, ringkasan semua data
    public function index()
    {
        return response()->json([
            'profil_gereja'   => ProfilGereja::first(['nama_gereja', 'alamat', 'foto']),
            'jadwal_utama'    => Jadwal::aktif()->where('jenis', 'ibadah_umum')->orderBy('hari')->take(5)->get(),
            'pengumuman'      => Pengumuman::published()->publik()->orderByDesc('published_at')->take(3)->get(),
            'renungan_hari_ini' => RenunganHarian::published()->hariIni()->first(['judul', 'ayat_alkitab']),
            'galeri_terbaru'  => Galeri::orderByDesc('tanggal_kegiatan')->take(6)->get()->map(fn($g) => [
                'id'    => $g->id,
                'judul' => $g->judul,
                'foto'  => asset('storage/' . $g->foto),
                'tanggal_kegiatan' => $g->tanggal_kegiatan,
            ]),
        ]);
    }
}
