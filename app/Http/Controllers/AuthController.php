<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){

        // $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required'
        // ]);

        // $user = User::where('email', $request->input('email'))->first();

        // if ($user && $request->input('password') === $user->password) {
        // //if login successfull generate a key and save this into database
        // $generatedKey = \Illuminate\Support\Str::random(32);
        // // Save the generated key into the user's record in the database
        // $user->remember_token = $generatedKey;
        // $user->save();
        // return response()->json([
        // 'message' => 'Login successful',
        // 'key' => $user->remember_token,
        // ], 200);
        // }

        // return response()->json(['error' => 'Invalid credentials'], 401);

        // Validate email and password input

    // Validate email and password input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    // Manually retrieve the user by email
    $user = User::where('email', $request->input('email'))->first();

    // Check if the user exists and if the plain text password matches
    if ($user && $request->input('password') === $user->password) {

        // Log the user in manually using Auth::login()
        Auth::login($user);

        // Generate a remember token if needed
        if (!$user->getRememberToken()) {
            $user->setRememberToken(\Illuminate\Support\Str::random(48));
            $user->save();
        }

        return response()->json([
            'status'=>true,
            'role'=>$user->role,
            'message' => 'Login successful',
            'key' => $user->getRememberToken(),  // Access the remember token
        ], 200);
    }

    // Return error if credentials don't match
    return response()->json(['status'=>false,'message' => 'Invalid credentials'], 401);


    }
}
