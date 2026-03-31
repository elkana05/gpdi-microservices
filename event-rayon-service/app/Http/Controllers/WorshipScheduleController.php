<?php

namespace App\Http\Controllers;

use App\Models\WorshipSchedule;
use Illuminate\Http\Request;

class WorshipScheduleController extends ApiController
{
    public function index()
    {
        $schedules = WorshipSchedule::paginate(10);
        
        $meta = [
            'current_page' => $schedules->currentPage(),
            'per_page'     => $schedules->perPage(),
            'total'        => $schedules->total(),
            'last_page'    => $schedules->lastPage()
        ];

        return $this->successResponse($schedules->items(), 'Worship schedules retrieved successfully', 200, $meta);
    }

    public function show($id)
    {
        $schedule = WorshipSchedule::find($id);

        if (!$schedule) {
            return $this->errorResponse('Resource not found', 404);
        }

        return $this->successResponse($schedule, 'Worship schedule retrieved successfully');
    }
}