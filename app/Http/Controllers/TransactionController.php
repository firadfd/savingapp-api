<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // Get total amount of the authenticated user
    public function getUserTotalAmount(Request $request)
    {
        $user = $request->user;

        $totalAmount = Transaction::where('user_id', $user->id)->sum('amount');

        return response()->json([
            'status' => true,
            'message' => 'Total amount retrieved successfully',
            'totalAmount' => strval($totalAmount),
        ]);
    }

    // Get total amount of all users
    public function getAllUsersTotalAmount()
    {
        $totalAmount = Transaction::sum('amount');

        return response()->json([
            'status' => true,
            'message' => 'Total amount retrieved successfully',
            'totalAmount' => strval($totalAmount)
        ]);
    }

    // Add a transaction (only for admin users)
    public function addTransaction(Request $request)
    {
        $user = $request->user;

        if ($user->role !== 'admin') {
            return response()->json(['status' => false,
            'message' => 'Unauthorized'], 403);
        }

        // Validate the request
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'from' => 'required|string',
            'date' => 'required|date',
            'month'=>'required|string',
        ]);

        // Create a new transaction
        Transaction::create([
            'user_id' => $validatedData['user_id'],
            'amount' => $validatedData['amount'],
            'from' => $validatedData['from'],
            'date' => $validatedData['date'],
            'month' => $validatedData['month'],
            'total' => Transaction::where('user_id', $validatedData['user_id'])->sum('amount') + $validatedData['amount']
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Transaction added successfully',
            'data' => $validatedData
        ]);
    }
}
