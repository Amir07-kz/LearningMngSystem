<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionTheme;
use App\Models\UserAnswer;
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
        if (Auth::check())
        {
            $user = Auth::user();
            $isAdmin = $user->isAdmin();
        } else {
            redirect('/');
        }

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
        $code = $request->input('course_code');
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

    // перенести в SlideController
    public function saveAnswers(Request $request)
    {
        try {
            $userId = auth()->id();
            $answers = $request->input('answers', []);

            foreach ($answers as $questionId => $answerId) {
                UserAnswer::create([
                    'user_id' => $userId,
                    'question_id' => $questionId,
                    'answer_id' => $answerId
                ]);
            }
            return response()->json(['success' => 'Ответ(-ы) сохранены!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка при сохранении ответ(-ов): ' . $e->getMessage()], 500);
        }
    }

    public function showJoinedUsers($courseId)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        $course = Course::with('users')->findOrFail($courseId);
        $joinedCourses = $user->joinedCourses()->get();
        $isAuthenticated = Auth::check();

        return view('joined_user_list', [
            'course' => $course,
            'isAdmin' => $isAdmin,
            'joinedCourses' => $joinedCourses,
            'is_authenticated' => $isAuthenticated
        ]);
    }

    public function showUserStatistics($courseId, $userId)
    {
        $questions = Question::where('id', $courseId)->get();

        $themes = $questions->groupBy('theme');
        dd($themes);

        $statistics = [];

        foreach ($themes as $theme => $questionsGroup) {
            $questionIds = $questionsGroup->pluck('id');

            $correctCount = UserAnswer::where('user_id', $userId)
                ->whereIn('question_id', $questionIds)
                ->join('answers', 'user_answers.answer_id', '=', 'answers.id')
                ->where('answers.is_correct', true)
                ->count();

            $statistics[] = [
                'theme' => $theme,
                'correctCount' => $correctCount
            ];
        }

        return view('user_statistics', [
            'statistics' => $statistics,
            'userId' => $userId,
            'courseId' => $courseId,
        ]);
    }
}
