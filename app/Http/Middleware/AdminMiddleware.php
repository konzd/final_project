<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
{
    $user = Auth::guard('api')->user();
    Log::info('User in Middleware:', [$user]);

    if ($user && $user->role === 'admin') {
        return $next($request);
    }

    return response()->json([
        'success' => false,
        'message' => 'Access denied. Admins only.'
    ], 403);
}

}
