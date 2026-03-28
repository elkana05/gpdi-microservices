<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RayonSchedule extends Model {
    protected $fillable = ['rayon_id', 'title', 'description', 'location', 'event_date', 'start_time', 'end_time', 'created_by_user_id', 'created_by_name'];
}