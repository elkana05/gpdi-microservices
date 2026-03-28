<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RenunganHarian extends Model
{
    protected $table = 'renungan_harian';

    protected $fillable = [
        'judul', 'ayat_alkitab', 'isi_renungan',
        'tanggal_publikasi', 'status', 'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_publikasi' => 'date',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_publikasi', today());
    }
}
