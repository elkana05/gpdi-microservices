<?php

namespace App\Http\Controllers;

use App\Models\WorshipSchedule;
use App\Models\ActivitySchedule;
use Illuminate\Http\Request;

class PublicEventController extends ApiController
{
    public function getWorshipSchedules()
    {
        $schedules = WorshipSchedule::where('status_publish', 'published')->paginate(10);
        
        $meta = [
            'current_page' => $schedules->currentPage(),
            'per_page'     => $schedules->perPage(),
            'total'        => $schedules->total(),
            'last_page'    => $schedules->lastPage()
        ];

        return $this->successResponse($schedules->items(), 'Worship schedules retrieved successfully', 200, $meta);
    }

    public function getActivitySchedules()
    {
        $activities = ActivitySchedule::where('status_publish', 'published')->paginate(10);
        
        $meta = [
            'current_page' => $activities->currentPage(),
            'per_page'     => $activities->perPage(),
            'total'        => $activities->total(),
            'last_page'    => $activities->lastPage()
        ];

        return $this->successResponse($activities->items(), 'Activity schedules retrieved successfully', 200, $meta);
    }
}