<?php

use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SlideController;

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

Route::get('/courses/edit', [CourseController::class, 'edit']);

//Route::post('/courses/edi);

Route::get('/courses/list', [CourseController::class, 'courseList'])->name('courses_list');

Route::post('/courses/{course}/slide/create', [SlideController::class, 'store'])->name('slides.store');

Route::get('/courses/{course}/slide/create', [SlideController::class, 'index'])->name('slide.create');

Route::get('/courses/{course}/slide/{slide}', [SlideController::class, 'show'])->name('slides.show');

//Route::get('/courses/{course}/slide/edit', [SlideController::class, 'index']);

//Route::post('/courses/{course}/slides', [SlideController::class, 'store']);
//
//Route::get('/slides/{slide}/edit', [SlideController::class, 'edit'])->name('slides.edit');
//
//Route::put('/slides/{slide}', [SlideController::class, 'update'])->name('slides.update');
//
//Route::get('/courses/{course}/slides/create', [SlideController::class, 'create'])->name('slides.create');
//
//Route::get('/courses/{course}/slides/{slide}', [SlideController::class, 'index']);