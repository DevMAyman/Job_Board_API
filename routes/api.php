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

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        // Create a token with Sanctum and attach it to the user
        $token = $user->createToken($request->device_name, ['userData' => $userData]);

        // Set the token as an HTTP-only cookie
        $cookie = cookie('token', $token->plainTextToken, 60); // Set cookie for 60 minutes

        return response()->json([
            'message' => 'Authenticated',
            'token' => [
                'plainTextToken' => $token->plainTextToken
            ]
        ])->withCookie($cookie);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});

use Illuminate\Support\Facades\Crypt;

Route::post('/decode-token', function (Request $request) {
    $encryptedToken = $request->input('token');

    try {
        // Decrypt the token
        $decodedToken = Crypt::decryptString($encryptedToken);

        // Return decoded token data
        return response()->json($decodedToken);
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        // Handle decryption error
        return response()->json(['error' => 'Invalid token'], 400);
    }
});


Route::apiResource('users', UserController::class);

Route::middleware('auth:sanctum')->group(function () {
    // To delete all users if we create a seeder and then we want to delete all 
    Route::delete('/users/deleteAll', [UserController::class, 'deleteAllUsers']);
});