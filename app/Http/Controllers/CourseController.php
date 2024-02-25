<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return view('courses_create');
    }

    public function courseList()
    {
        $courses = Course::all();
        return view('courses_list', ['courses' => $courses]);
    }

    // Метод для сохранения нового курса
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
        $course->save();

        return redirect()->route('courses_list')->with('success', 'Курс успешно создан');
    }
}
