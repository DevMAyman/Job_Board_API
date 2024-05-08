<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/sanctum/token', function (Request $request) {
    if (Auth::attempt($request->only('email', 'password'))) {
        $user = Auth::user();

        $userData = [
            'name' => $user->name,
            'role' => $user->role,
        ];

        $token = $user->createToken($request->device_name, ['userData' => $userData]);

        return response()->json([
            'message' => 'Authenticated',
            'token' => [
                'plainTextToken' => $token->plainTextToken,
                'user' => $userData 
            ]
        ]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});

// Custom middleware for role-based authorization
// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

Route::middleware(['auth:sanctum', 'role'])->group(function () {
    Route::delete('/users/deleteAll', [UserController::class, 'deleteAllUsers']);
    Route::apiResource('users', UserController::class);
});

