<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit sesuai migration
    protected $table = 't_pengumuman';

    // Kolom-kolom yang diizinkan untuk diisi secara massal (mass assignment)
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