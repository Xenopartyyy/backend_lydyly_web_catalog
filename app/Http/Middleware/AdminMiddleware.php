<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user sudah login dan username mengandung 'admin'
        if (Auth::check() && Str::contains(strtolower(Auth::user()->name), 'admin')) {
            return $next($request);
        }

        return redirect('/dashboard/lydyly2')->with('error', 'Anda tidak memiliki akses!');
    }
}