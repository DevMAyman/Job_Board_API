<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie; // Add this line

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 

Route::post('/sanctum/token', function (Request $request) {
    if (Auth::attempt($request->only('email', 'password'))) {
        $user = Auth::user();

        // Customize the token payload
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            // Add any other user data you want to include
        ];

        // Create a token with Sanctum and attach it to the user
        $token = $user->createToken($request->device_name, ['userData' => $userData]);

        // Set the token as an HTTP-only cookie
        $cookie = cookie('token', $token->plainTextToken, 60); // Set cookie for 60 minutes

        return response()->json([
            'message' => 'Authenticated',
            'token' => [
                'plainTextToken' => $token->plainTextToken,
                'user' => $userData // Include user data in the response
            ]
        ])->withCookie($cookie);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});



Route::middleware('auth:sanctum')->group(function () {
    // To delete all users if we create a seeder and then we want to delete all 
    Route::delete('/users/deleteAll', [UserController::class, 'deleteAllUsers']);
    Route::apiResource('users', UserController::class);
});
