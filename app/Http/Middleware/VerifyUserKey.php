<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyUserKey
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
         // Retrieve the key from the headers
         $key = $request->header('key');

         // Check if the key is present
         if (!$key) {
             return response()->json([
                 'status' => false,
                 'message' => 'Key is missing from the headers.'
             ], 400);
         }

         // Find the user by the key (remember token)
         $user = User::where('remember_token', $key)->first();

         // If user is not found, return an error
         if (!$user) {
             return response()->json([
                 'status' => false,
                 'message' => 'Invalid key. User not found.'
             ], 401);
         }

         // If user is found, attach the user to the request for future use
         $request->merge(['user' => $user]);

         // Proceed to the next request
         return $next($request);
    }
}
