<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    // WAJIB: Beri tahu Laravel nama tabel yang benar
    protected $table = 't_pengumuman';

    protected $fillable = [
        'judul', 
        'isi', 
        'lampiran', 
        'id_pembuat', 
        'scope', 
        'id_rayon', 
        'status', 
        'published_at'
    ];

    // Opsional namun disarankan: memastikan published_at otomatis diperlakukan sebagai objek Carbon/DateTime
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}