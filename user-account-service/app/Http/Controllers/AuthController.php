<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // Cek kredensial
        if (! auth('api')->attempt($validator->validated())) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        // FIX: Gunakan Eager Loading 'with' agar hanya 1x query ke database
        $user = User::with(['roles', 'profile'])->find(auth('api')->id());

        $role = $user->roles->first();
        $roleName = $role->name ?? null;
        $fullName = $user->profile->full_name ?? null;
        $rayonId  = $user->profile->rayon_id ?? null;

        // Buat Custom Claims
        $customClaims = [
            'name'     => $fullName,
            'role'     => $roleName,
            'id_rayon' => $rayonId
        ];

        // Generate token
        $token = JWTAuth::claims($customClaims)->fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => [
                    'id' => $user->id,
                    'name' => $fullName,
                    'email' => $user->email,
                    'role' => $roleName,
                    'id_rayon' => $rayonId
                ]
            ]
        ], 200);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['status' => 'success', 'message' => 'Successfully logged out'], 200);
    }

    public function me()
    {
        $user = User::with(['roles', 'profile'])->find(auth('api')->id());

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->profile->full_name ?? null,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? null,
                'id_rayon' => $user->profile->rayon_id ?? null
            ]
        ], 200);
    }
}
