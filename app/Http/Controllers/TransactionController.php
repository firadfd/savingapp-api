<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller {
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

        $user = User::findOrFail( $request->user_id);

        if($user){
            try {

                // Create the transaction
                $transaction = new Transaction();
                $transaction->user_id = $request->user_id;
                $transaction->amount = $request->amount;
                $transaction->from = $request->from;
                $transaction->date = $request->date;
                $transaction->month = $request->month;
                $transaction->save();

                // Return a successful response
                return response()->json(['message' => 'Transaction added successfully', 'transaction' => $transaction], 201);
            } catch (ModelNotFoundException $e) {
                // Handle the case when user ID does not exist (although it's already validated)
                return response()->json(['error' => 'User not found'], 404);
            } catch (\Exception $e) {
                // Handle any other exceptions
                return response()->json(['error' => 'An error occurred while adding the transaction'], 500);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }


    }


    public function getUserTransactions(Request $request)
    {
        // Get the user ID from the request (assuming it's passed as a parameter)
        $user = $request->user;

        $userId = $user->id;

        // Find the user by ID
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Fetch all transactions for the user
        $transactions = Transaction::where('user_id', $userId)->get();

        // If no transactions are found
        if ($transactions->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No transactions found for this user',
                'data' => []
            ], 404);
        }

        // Return transactions in an array format
        return response()->json([
            'status' => true,
            'message' => 'Transactions retrieved successfully',
            'data' => $transactions->toArray()
        ], 200);
    }


    public function getAllUserTransactions(Request $request)
    {
        // Get the user ID from the request (assuming it's passed as a parameter)
        $user = $request->user;
        $userId = $user->id;

        // Find the user by ID
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Fetch all transactions from the Transaction model
        $transactions = Transaction::all();

        // If no transactions are found
        if ($transactions->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No transactions found',
                'data' => []
            ], 404);
        }

        // Return all transactions in an array format
        return response()->json([
            'status' => true,
            'message' => 'All transactions retrieved successfully',
            'data' => $transactions->toArray()
        ], 200);
    }




    public function updateTransaction(Request $request, $id)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'user_id' => 'required|integer|exists:users,id',
        'date' => 'required|date',
        'amount' => 'required|numeric',
        'from' => 'required|string',
        'month' => 'required|string',
    ]);

    // Find the transaction by ID
    $transaction = Transaction::find($id);

    // If the transaction doesn't exist, return a 404 response
    if (!$transaction) {
        return response()->json([
            'status' => false,
            'message' => 'Transaction not found'
        ], 404);
    }

    // Update the transaction with the validated data
    $transaction->user_id = $validatedData['user_id'];
    $transaction->date = $validatedData['date'];
    $transaction->amount = $validatedData['amount'];
    $transaction->from = $validatedData['from'];
    $transaction->month = $validatedData['month'];

    // Save the updated transaction
    $transaction->save();

    // Return a success response
    return response()->json([
        'status' => true,
        'message' => 'Transaction updated successfully',
        'data' => $transaction
    ], 200);
}



public function deleteTransaction($id)
{
    // Find the transaction by ID
    $transaction = Transaction::find($id);

    if (!$transaction) {
        return response()->json([
            'status' => false,
            'message' => 'Transaction not found'
        ], 404);
    }

    // Delete the transaction
    $transaction->delete();

    return response()->json([
        'status' => true,
        'message' => 'Transaction deleted successfully'
    ], 200);
}


}
