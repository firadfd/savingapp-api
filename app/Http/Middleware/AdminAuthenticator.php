<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminAuthenticator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the key from the request header
        $key = $request->header('key');

        // Check if the key is present
        if (!$key) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Find the user by the key
        $user = User::where('remember_token', $key)->first();

        // Check if the user exists and if they are an admin
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // If user is not an admin, return unauthorized response
        return response()->json(['error' => 'Unauthorized'], 403);

    }
}
