<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApplicationController; // Corrected namespace
use App\Http\Controllers\Api\JobListingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/applications', [ApplicationController::class, 'store']);

Route::apiResource('/jobs', JobListingController::class);
