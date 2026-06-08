<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::middleware('staff')->group(function () {
        Route::get('/staff/dashboard', function () {
            return response()->json([
                'message' => 'Welcome to Staff Dashboard',
                'user' => auth()->user()
            ]);
        });
    });
});
