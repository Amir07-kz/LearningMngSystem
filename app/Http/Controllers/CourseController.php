<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        return view('course_create');
    }

    public function courseList()
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();
        $createdCourses = $user->courses()->get();
        $joinedCourses = $user->joinedCourses()->get();
        $isAuthenticated = Auth::check();

        return view('courses_list', [
            'courses' => $createdCourses,
            'joinedCourses' => $joinedCourses,
            'isAdmin' => $isAdmin,
            'is_authenticated' => $isAuthenticated
        ]);
    }

    public function myCourses()
    {
        $user = Auth::user();
        $joinedCourses = $user->joinedCourses()->with('slides')->get();
        return view('my_courses', compact('joinedCourses'));
    }

    public function joinToCourse(Request $request) {
        $code = $request->input('code');
        $course = Course::where('join_code', $code)->first();

        if ($course) {
            Auth::user()->joinedCourses()->attach($course->id);
            return back()->with('success', 'Вы успешно присоединились к курсу!');
        } else {
            return back()->with('error', 'Курс с таким кодом не найден.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer',
        ]);

        $course = new Course();
        $course->title = $request->title;
        $course->description = $request->description;
        $course->duration = $request->duration;
        $course->join_code = Str::random(10);
        $course->save();

        return redirect()->route('courses_list')->with('success', 'Курс успешно создан');
    }

    public function create($courseId)
    {
        return view('courses.slides.create', ['courseId' => $courseId]);
    }

}
