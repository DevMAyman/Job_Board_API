<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//to delete all users if we create seeder and then we want to delete all 
Route::delete('/users/deleteAll', [UserController::class, 'deleteAllUsers']);
Route::apiResource('users', UserController::class);
