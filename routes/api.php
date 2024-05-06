<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::delete('/users/deleteAll', [UserController::class, 'deleteAllUsers']);
Route::apiResource('users', UserController::class);
