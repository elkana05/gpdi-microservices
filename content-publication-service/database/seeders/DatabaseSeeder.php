<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Profil Gereja awal
        DB::table('profil_gereja')->insertOrIgnore([
            'nama_gereja'    => 'GPdI Jemaat Sibulele – Balige',
            'alamat'         => 'Jl. Sibulele No. 1, Balige, Kab. Toba, Sumatera Utara',
            'visi'           => 'Menjadi gereja yang bertumbuh dalam iman, kasih, dan pelayanan.',
            'misi'           => 'Memberitakan Injil, membina jemaat, dan melayani masyarakat.',
            'no_telepon'     => '0821-0000-0000',
            'email'          => 'gpdi.sibulele@email.com',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Jadwal Ibadah
        $jadwal = [
            ['nama_kegiatan' => 'Ibadah Minggu Pagi', 'jenis' => 'ibadah_umum', 'hari' => 'Minggu', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:00', 'lokasi' => 'Gedung Utama'],
            ['nama_kegiatan' => 'Ibadah Minggu Sore', 'jenis' => 'ibadah_umum', 'hari' => 'Minggu', 'waktu_mulai' => '17:00', 'waktu_selesai' => '19:00', 'lokasi' => 'Gedung Utama'],
            ['nama_kegiatan' => 'Ibadah Rabu (Pendalaman Alkitab)', 'jenis' => 'ibadah_umum', 'hari' => 'Rabu', 'waktu_mulai' => '19:00', 'waktu_selesai' => '21:00', 'lokasi' => 'Gedung Utama'],
            ['nama_kegiatan' => 'Ibadah Pemuda', 'jenis' => 'kegiatan_khusus', 'hari' => 'Sabtu', 'waktu_mulai' => '16:00', 'waktu_selesai' => '18:00', 'lokasi' => 'Aula Pemuda'],
        ];

        foreach ($jadwal as $j) {
            DB::table('jadwal')->insertOrIgnore(array_merge($j, [
                'is_aktif'   => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Informasi Pelayanan
        $pelayanan = [
            ['nama_pelayanan' => 'Sekolah Minggu', 'deskripsi' => 'Pelayanan pendidikan rohani bagi anak-anak usia 4–12 tahun.', 'target_usia' => '4–12 tahun', 'jadwal' => 'Setiap Minggu, 08:00–10:00'],
            ['nama_pelayanan' => 'Pemuda GPdI', 'deskripsi' => 'Wadah pembinaan dan persekutuan bagi pemuda gereja.', 'target_usia' => '17–35 tahun', 'jadwal' => 'Setiap Sabtu, 16:00–18:00'],
            ['nama_pelayanan' => 'Komisi Wanita', 'deskripsi' => 'Pelayanan pembinaan dan doa bagi kaum ibu jemaat.', 'target_usia' => 'Dewasa', 'jadwal' => 'Setiap Jumat, 10:00–12:00'],
        ];

        foreach ($pelayanan as $p) {
            DB::table('informasi_pelayanan')->insertOrIgnore(array_merge($p, [
                'is_aktif'   => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Seeder berhasil! Data awal GPdI sudah tersedia.');
    }
}
