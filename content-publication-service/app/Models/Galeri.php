<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'foto',
        'tanggal_kegiatan', 'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
    ];

    public function getFotoUrlAttribute(): string
    {
        return asset('storage/' . $this->foto);
    }
}
