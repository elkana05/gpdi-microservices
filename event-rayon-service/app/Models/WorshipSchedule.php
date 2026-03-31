<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorshipSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'event_date',
        'start_time',
        'end_time',
        'status_publish'
    ];

    protected $casts = [
        'event_date' => 'date',
    ];
}