<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rayon extends Model
{
    use HasFactory;

<<<<<<< Updated upstream
    protected $table = 'm_rayon';

    protected $fillable = [
        'nama_rayon',
        'id_ketua_rayon',
        'keterangan',
    ];
=======
    protected $fillable = [
        'name',
        'area',
        'ketua_user_id'
    ];

    // Relasi 1-to-Many ke tabel rayon_schedules
    public function rayonSchedules()
    {
        return $this->hasMany(RayonSchedule::class, 'rayon_id');
    }
>>>>>>> Stashed changes
}