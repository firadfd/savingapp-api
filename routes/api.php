<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/sign-in', [AuthController::class, 'login']);


Route::middleware(['verifyKey'])->group(function () {

    // User info route
    Route::get('/user-info', [AuthController::class, 'getUserInfo']);

    //get all user
    Route::get('/all-users', [AuthController::class, 'getAllUsers']);

    // Route to get total amount of the authenticated user
    Route::get('/user-total', [TransactionController::class, 'getUserTotalAmount']);

    // Route to get total amount of all users
    Route::get('/all-users-total', [TransactionController::class, 'getAllUsersTotalAmount']);

    //get all transactions
    Route::get('/all-transactions', [TransactionController::class, 'getAllTransactions']);


    //get user transactions
    Route::get('/user-transactions', [TransactionController::class, 'getUserTransactions']);

    // Only admins can get transactions
    Route::get('/all-transactions', [TransactionController::class, 'getAllUserTransactions']);
    // ->middleware('admin');

    // Only admins can add transactions
    Route::post('/add-transaction', [TransactionController::class, 'addTransaction'])
    ->middleware('admin');

    // Only admins can update transactions
    Route::put('/update-transaction/{id}', [TransactionController::class, 'updateTransaction'])
    ->middleware('admin');

    // Only admins can delete transactions
    Route::delete('/delete-transaction/{id}', [TransactionController::class, 'deleteTransaction'])
    ->middleware('admin');



});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
