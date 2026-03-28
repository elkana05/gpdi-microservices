<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformasiPelayanan extends Model
{
    protected $table = 'informasi_pelayanan';

    protected $fillable = [
        'nama_pelayanan', 'deskripsi', 'target_usia',
        'jadwal', 'penanggung_jawab', 'foto', 'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
