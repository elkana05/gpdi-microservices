<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role;

class InternalController extends Controller
{
    public function showUser($id)
    {
        $user = User::with(['profile', 'roles'])->find($id);
        if (!$user) return response()->json(['status' => 'error', 'message' => 'Resource not found'], 404);
        
        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully', 'data' => $user, 'meta' => null], 200);
    }

    public function getRoles()
    {
        $roles = Role::all();
        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully', 'data' => $roles, 'meta' => null], 200);
    }
}