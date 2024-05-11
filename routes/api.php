<?php

use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\JobListingController;
use App\Http\Controllers\API\LogoutController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
// use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Corrected namespace
use Illuminate\Support\Facades\Route;

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



Route::get('/usersPaginate', function (Request $request) {
    return User::search($request->input('query'))->paginate(15);
});