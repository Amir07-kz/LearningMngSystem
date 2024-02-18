<?php

use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthentificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
*/

Route::get('/registration', [RegistrationController::class, 'index']);

Route::post('/registration', [RegistrationController::class, 'store']);

Route::get('/authentification', [AuthentificationController::class, 'index']);

Route::post('/authentification', [AuthentificationController::class, 'authenticate']);

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

Route::post('/logout', [AuthentificationController::class, 'logout']);