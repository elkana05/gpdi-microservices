<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FamilyMemberController extends Controller
{
    private function checkRole() {
        $role = auth('api')->user()->roles()->first();
        return $role && $role->name === 'jemaat_aktif';
    }

    public function index()
    {
        $members = auth('api')->user()->familyMembers;
        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully', 'data' => $members, 'meta' => null], 200);
    }

    public function store(Request $request)
    {
        if (!$this->checkRole()) return response()->json(['status' => 'error', 'message' => 'You do not have permission to access this resource'], 403);

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string', 'relationship' => 'required|string',
            'gender' => 'required|string', 'birth_date' => 'required|date'
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);

        $member = auth('api')->user()->familyMembers()->create($validator->validated());
        return response()->json(['status' => 'success', 'message' => 'Family member added successfully', 'data' => $member, 'meta' => null], 201);
    }

    public function update(Request $request, $id)
    {
        if (!$this->checkRole()) return response()->json(['status' => 'error', 'message' => 'You do not have permission to access this resource'], 403);
        
        $member = auth('api')->user()->familyMembers()->find($id);
        if (!$member) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);

        $member->update($request->all());
        return response()->json(['status' => 'success', 'message' => 'Family member updated successfully', 'data' => $member, 'meta' => null], 200);
    }

    public function destroy($id)
    {
        if (!$this->checkRole()) return response()->json(['status' => 'error', 'message' => 'You do not have permission to access this resource'], 403);

        $member = auth('api')->user()->familyMembers()->find($id);
        if (!$member) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);

        $member->delete();
        return response()->json(['status' => 'success', 'message' => 'Family member deleted successfully', 'data' => (object)[], 'meta' => null], 200);
    }
}