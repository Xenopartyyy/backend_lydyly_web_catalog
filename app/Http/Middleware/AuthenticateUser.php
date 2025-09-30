<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateUser
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login
        if (!session()->has('auth')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}

