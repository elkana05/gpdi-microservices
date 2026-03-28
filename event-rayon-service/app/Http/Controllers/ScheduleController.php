<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\WorshipSchedule;
use App\Models\ActivitySchedule;
use App\Models\RayonSchedule;

class ScheduleController extends Controller
{
    /* =========================================================
       ENDPOINT PUBLIK (Tanpa Middleware)
       ========================================================= */
    public function getPublicWorshipSchedules()
    {
        $schedules = WorshipSchedule::where('status_publish', 'published')->get();
        return response()->json(['status' => 'success', 'message' => 'Worship schedules retrieved', 'data' => $schedules, 'meta' => null], 200);
    }

    public function getPublicActivitySchedules()
    {
        $schedules = ActivitySchedule::where('status_publish', 'published')->get();
        return response()->json(['status' => 'success', 'message' => 'Activity schedules retrieved', 'data' => $schedules, 'meta' => null], 200);
    }

    /* =========================================================
       ENDPOINT PRIVAT: IBADAH RAYA (Hanya Tampilan / Read-Only)
       ========================================================= */
    public function getWorshipSchedules(Request $request)
    {
        $schedules = WorshipSchedule::all();
        return response()->json(['status' => 'success', 'message' => 'Worship schedules retrieved', 'data' => $schedules, 'meta' => null], 200);
    }

    public function getWorshipScheduleById(Request $request, $id)
    {
        $schedule = WorshipSchedule::find($id);
        if (!$schedule) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);
        
        return response()->json(['status' => 'success', 'message' => 'Worship schedule retrieved', 'data' => $schedule, 'meta' => null], 200);
    }

    /* =========================================================
       ENDPOINT PRIVAT: JADWAL RAYON (Hak Kelola Ketua Rayon)
       ========================================================= */
    public function getRayonSchedules(Request $request)
    {
        $schedules = RayonSchedule::all();
        return response()->json(['status' => 'success', 'message' => 'Rayon schedules retrieved', 'data' => $schedules, 'meta' => null], 200);
    }

    public function getRayonScheduleById(Request $request, $id)
    {
        $schedule = RayonSchedule::find($id);
        if (!$schedule) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);
        
        return response()->json(['status' => 'success', 'message' => 'Rayon schedule retrieved', 'data' => $schedule, 'meta' => null], 200);
    }

    public function storeRayonSchedule(Request $request)
    {
        $user = $request->auth_user;
        if ($user['role'] !== 'ketua_rayon') {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to access this resource'], 403);
        }

        $validator = Validator::make($request->all(), [
            'rayon_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);

        $data = $validator->validated();
        $data['created_by_user_id'] = $user['id'];
        $data['created_by_name'] = $user['name'];

        $schedule = RayonSchedule::create($data);

        return response()->json(['status' => 'success', 'message' => 'Rayon schedule created successfully', 'data' => $schedule, 'meta' => null], 201);
    }

    public function updateRayonSchedule(Request $request, $id)
    {
        $user = $request->auth_user;
        if ($user['role'] !== 'ketua_rayon') {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to access this resource'], 403);
        }

        $schedule = RayonSchedule::find($id);
        if (!$schedule) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);

        $validator = Validator::make($request->all(), [
            'rayon_id' => 'sometimes|required|integer',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'sometimes|required|string|max:255',
            'event_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i:s,H:i',
            'end_time' => 'nullable|date_format:H:i:s,H:i'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);

        $schedule->update($validator->validated());

        return response()->json(['status' => 'success', 'message' => 'Rayon schedule updated successfully', 'data' => $schedule, 'meta' => null], 200);
    }

    public function destroyRayonSchedule(Request $request, $id)
    {
        $user = $request->auth_user;
        if ($user['role'] !== 'ketua_rayon') {
            return response()->json(['status' => 'error', 'message' => 'You do not have permission to access this resource'], 403);
        }

        $schedule = RayonSchedule::find($id);
        if (!$schedule) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);

        $schedule->delete();

        return response()->json(['status' => 'success', 'message' => 'Rayon schedule deleted successfully', 'data' => (object)[], 'meta' => null], 200);
    }
}