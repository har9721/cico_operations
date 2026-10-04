<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credential = $request->only('email', 'password');

        $validate = Validator::make($credential, [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors(),
            ], 422);
        }

        try {
            $verifiedUser = User::where('email', $credential['email'])->first();

            if ($verifiedUser && Hash::check($credential['password'], $verifiedUser->password)) {
                $token = JWTAuth::fromUser($verifiedUser);

                return response()->json([
                    'status' => 'success',
                    'token' => $token,
                    'data' => User::where('email', $credential['email'])->first(),
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,
                ], 200);
            } else {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }
}
