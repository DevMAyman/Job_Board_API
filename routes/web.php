<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApplicationController;

Route::get('/', function () {
    return view('welcome');
});

