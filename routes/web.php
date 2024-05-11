<?php

use App\Http\Controllers\API\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::apiResource('applications', ApplicationController::class);
