<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // validasi input
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        // ambil credential
        $credentials = $request->only('name', 'password');

        // coba login
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // kirim token
        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
            /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $guard */
            $guard = auth('api');

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $guard->factory()->getTTL() * 60,
        ]);
    }
}
