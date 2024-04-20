<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SlideController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
*/

Route::post('/api/send-request', [ApiController::class, 'sendRequest'])->name('api.sendRequest');

Route::get('/registration', [RegistrationController::class, 'index'])->name('registration');

Route::post('/registration', [RegistrationController::class, 'store']);

Route::get('/login', [LoginController::class, 'index'])->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/', [HomeController::class, 'index'])->name('/');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('/courses/join', [CourseController::class, 'joinToCourse'])->name('join_course');

Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('my_courses');

Route::get('/courses/create', [CourseController::class, 'index'])->name('create_course');

Route::post('/courses/create', [CourseController::class, 'store']);

Route::get('/courses/edit', [CourseController::class, 'edit']);

Route::get('/courses/list', [CourseController::class, 'courseList'])->name('courses_list');

Route::get('/account', [UserController::class, 'index'])->name('personal_account');

Route::post('/account', [UserController::class, 'update'])->name('update_account');

Route::get('/courses/{course}/slide/', [SlideController::class, 'firstSlide'])->name('slide.first');

Route::post('/courses/{course}/slide/create', [SlideController::class, 'store'])->name('slides.store');

Route::get('/courses/{course}/slide/create', [SlideController::class, 'index'])->name('slide.create');

Route::get('/courses/{course}/slide/{slide}', [SlideController::class, 'show'])->name('slides.show'); // slide

Route::delete('/courses/{course}/slide/{slide}', [SlideController::class, 'remove'])->name('slide.remove');

Route::post('/courses/{course}/slide/{slide}', [SlideController::class, 'update'])->name('slide.update');

Route::delete('/courses/{course}/slide/{slide}/description/{descriptionId}', [SlideController::class, 'slideContentRemove'])->name('slide.content.remove');

Route::delete('/media/delete/{id}', [SlideController::class, 'deleteMedia'])->name('media.delete');

Route::delete('/questions/{question}', [SlideController::class, 'deleteQuestion'])->name('questions.delete');

Route::post('/save-answers', [CourseController::class, 'saveAnswers'])->name('save.answers');