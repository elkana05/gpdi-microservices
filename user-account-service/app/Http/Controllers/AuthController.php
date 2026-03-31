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

        // Cek kredensial
        if (! auth('api')->attempt($validator->validated())) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $user = auth('api')->user();
        $role = $user->roles()->first();

        // Buat Custom Claims untuk disisipkan ke dalam Token JWT
        $customClaims = [
            'name'     => $user->profile->full_name ?? null,
            'role'     => $role->name ?? null,
            'id_rayon' => $user->profile->rayon_id ?? null 
        ];

        // Cetak ulang token dengan membawa custom claims tersebut
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
                    'name' => $user->profile->full_name ?? null,
                    'email' => $user->email,
                    'role' => $role->name ?? null,
                    'id_rayon' => $user->profile->rayon_id ?? null
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
        $user = auth('api')->user();
        $role = $user->roles()->first();

        // PERBAIKAN: Format data disamakan persis dengan format login
        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->profile->full_name ?? null,
                'email' => $user->email,
                'role' => $role->name ?? null,
                'id_rayon' => $user->profile->rayon_id ?? null
            ],
            'meta' => null
        ], 200);
    }
}