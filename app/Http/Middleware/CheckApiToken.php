<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-API-TOKEN');

        if ($token !== config('app.api_token')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
