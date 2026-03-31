<?php

namespace App\Http\Controllers;

use App\Models\RayonSchedule;
use App\Http\Requests\StoreRayonScheduleRequest;
use App\Http\Requests\UpdateRayonScheduleRequest;
use Illuminate\Http\Request;

class RayonScheduleController extends ApiController
{
    // Cek Role Middleware Manual via Header API Gateway
    private function checkKetuaRayonRole(Request $request)
    {
        return $request->header('X-User-Role') === 'ketua_rayon';
    }

    public function index()
    {
        $schedules = RayonSchedule::with('rayon')->paginate(10);
        
        $meta = [
            'current_page' => $schedules->currentPage(),
            'per_page'     => $schedules->perPage(),
            'total'        => $schedules->total(),
            'last_page'    => $schedules->lastPage()
        ];

        return $this->successResponse($schedules->items(), 'Rayon schedules retrieved successfully', 200, $meta);
    }

    public function show($id)
    {
        $schedule = RayonSchedule::with('rayon')->find($id);

        if (!$schedule) {
            return $this->errorResponse('Resource not found', 404);
        }

        return $this->successResponse($schedule, 'Rayon schedule retrieved successfully');
    }

    public function store(StoreRayonScheduleRequest $request)
    {
        if (!$this->checkKetuaRayonRole($request)) {
            return $this->errorResponse('You do not have permission to access this resource', 403);
        }

        $data = $request->validated();
        
        // Tangkap identitas pembuat dari Header API Gateway
        $data['created_by_user_id'] = $request->header('X-User-Id');
        $data['created_by_name']    = $request->header('X-User-Name', 'Ketua Rayon');

        $schedule = RayonSchedule::create($data);

        return $this->successResponse($schedule, 'Rayon schedule created successfully', 201);
    }

    public function update(UpdateRayonScheduleRequest $request, $id)
    {
        if (!$this->checkKetuaRayonRole($request)) {
            return $this->errorResponse('You do not have permission to access this resource', 403);
        }

        $schedule = RayonSchedule::find($id);

        if (!$schedule) {
            return $this->errorResponse('Resource not found', 404);
        }

        $schedule->update($request->validated());

        return $this->successResponse($schedule, 'Rayon schedule updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        if (!$this->checkKetuaRayonRole($request)) {
            return $this->errorResponse('You do not have permission to access this resource', 403);
        }

        $schedule = RayonSchedule::find($id);

        if (!$schedule) {
            return $this->errorResponse('Resource not found', 404);
        }

        $schedule->delete();

        return $this->successResponse(null, 'Rayon schedule deleted successfully');
    }
}