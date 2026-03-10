<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (! $token = auth('api')->attempt($validator->validated())) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $user = auth('api')->user();
        $role = $user->roles()->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->profile->full_name ?? null,
                    'email' => $user->email,
                    'role' => $role->name ?? null
                ]
            ]
        ], 200);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['status' => 'success', 'message' => 'Successfully logged out', 'data' => [], 'meta' => null], 200);
    }

    public function me()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => auth('api')->user()->load(['profile', 'roles']),
            'meta' => null
        ], 200);
    }
}