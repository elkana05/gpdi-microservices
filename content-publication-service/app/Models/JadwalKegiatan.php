<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKegiatan extends Model
{
    use HasFactory;

    protected $table = 't_jadwal_kegiatan';

    protected $fillable = [
        'nama_kegiatan',
        'kategori',
        'tanggal',
        'waktu',
        'lokasi',
        'deskripsi',
        'id_pembuat'
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            // 'waktu' biasanya akan otomatis diperlakukan sebagai string berformat H:i:s
        ];
    }
}