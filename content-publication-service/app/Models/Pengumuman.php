<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $fillable = [
        'judul', 'isi', 'cakupan', 'rayon_id',
        'status', 'published_at', 'dibuat_oleh',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublik($query)
    {
        return $query->where('cakupan', 'publik');
    }
}
