<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/registration', [RegisterController::class, 'index']);

Route::post('/registration', [RegisterController::class, 'register']);