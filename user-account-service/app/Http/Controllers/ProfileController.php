<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $profile = auth('api')->user()->profile;
        return response()->json([
            'status' => 'success', 'message' => 'Data retrieved successfully',
            'data' => $profile ?? (object)[], 'meta' => null
        ], 200);
    }

    public function update(Request $request)
    {
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'rayon_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $validator->validated()
        );

        return response()->json(['status' => 'success', 'message' => 'Profile updated successfully', 'data' => $profile, 'meta' => null], 200);
    }
}