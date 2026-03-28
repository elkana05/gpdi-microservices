<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = [
        'nama_kegiatan', 'jenis', 'hari',
        'waktu_mulai', 'waktu_selesai',
        'lokasi', 'keterangan', 'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
