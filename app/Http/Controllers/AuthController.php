<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate email and password input
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Manually retrieve the user by email
            $user = User::where('email', $request->input('email'))->first();

            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Check if the plain text password matches
            if ($request->input('password') !== $user->password) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid password'
                ], 401);
            }

            // Log the user in manually using Auth::login()
            Auth::login($user);

            // Generate a remember token if needed
            if (!$user->getRememberToken()) {
                $user->setRememberToken(\Illuminate\Support\Str::random(48));
                $user->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'key' => $user->getRememberToken(),  // Access the remember token
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch validation errors
            return response()->json([
                'status' => false,
                'message' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Catch any other exception
            return response()->json([
                'status' => false,
                'message' => 'An error occurred during login. Please try again later.'
            ], 500);
        }
    }


    public function getUserInfo(Request $request)
    {
       // Check if the middleware has attached the user to the request
    if (isset($request->user)) {
        // Retrieve the user from the request (set by the middleware)
        $user = $request->user;

        // Return user information
        return response()->json([
            'status' => true,
            'message' => 'User information retrieved successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'profile_image' => $user->profile_image,
            ]
        ], 200);
    } else {
        // Handle the case where the user is not found
        return response()->json([
            'status' => false,
            'message' => 'User not found or invalid key'
        ], 404);
    }

    }

    public function getAllUsers(Request $request)
    {
        try {
            // Retrieve all users' IDs
            $users = User::all(['id', 'name', 'email']);

            // Check if users collection is empty
            if ($users->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No users found'
                ], 404);
            }

            // Return users with a success message
            return response()->json([
                'status' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
            ], 200);

        } catch (\Exception $e) {
            // Handle any potential errors
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while retrieving users: ' . $e->getMessage()
            ], 500);
        }
    }


}
