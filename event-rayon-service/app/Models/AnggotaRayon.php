<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaRayon extends Model
{
    use HasFactory;

    protected $table = 't_anggota_rayon';

    protected $fillable = [
        'id_rayon',
        'id_jemaat',
        'tanggal_bergabung',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bergabung' => 'date',
        ];
    }
}