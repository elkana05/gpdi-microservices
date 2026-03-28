<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorshipSchedule extends Model {
    protected $fillable = ['title', 'description', 'location', 'event_date', 'start_time', 'end_time', 'status_publish'];
}