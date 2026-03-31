<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RayonSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rayon_id',
        'title',
        'description',
        'location',
        'event_date',
        'start_time',
        'end_time',
        'created_by_user_id',
        'created_by_name'
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    // Relasi Many-to-1 balik ke tabel rayons
    public function rayon()
    {
        return $this->belongsTo(Rayon::class, 'rayon_id');
    }
}