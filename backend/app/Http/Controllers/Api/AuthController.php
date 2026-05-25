<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $token = $user->createToken('mobile-app')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }
}
