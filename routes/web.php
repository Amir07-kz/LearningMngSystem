<?php

use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseController;

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

Route::get('/login', [LoginController::class, 'index']);

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/', [HomeController::class, 'index'])->name('/');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/courses/create', [CourseController::class, 'index']);

Route::post('/courses/create', [CourseController::class, 'store']);

Route::get('/courses/list', [CourseController::class, 'courseList'])->name('courses_list');