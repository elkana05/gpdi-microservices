<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorshipSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',    // Kolom Baru
        'day_of_week', // Kolom Baru
        'description',
        'location',
        'event_date',
        'start_time',
        'end_time',
        'status_publish'
    ];
}