<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\LogoutController;
use App\Http\Controllers\API\ApplicationController; // Corrected namespace
use App\Http\Controllers\API\JobListingController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/login', [UserController::class, 'login']);
Route::delete('/users/deleteAll', function (Request $request) {
    if ($request->user()->role === 'employer') {
        return (new UserController())->deleteAllUsers($request);
    } else {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
})->middleware('auth:sanctum');

Route::apiResource('users', UserController::class)->middleware('auth:sanctum');


Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    // Route::post('login', 'login');

});
Route::post('logout', LogoutController::class)->middleware('auth:sanctum');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/applications', ApplicationController::class)->middleware('auth:sanctum');

Route::apiResource('/jobs', JobListingController::class)->middleware('auth:sanctum');
