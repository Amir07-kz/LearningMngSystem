<?php

namespace App\Http\Controllers;

use App\Models\Answer;
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
        $submissionToken = $request->input('submissionToken');
        if ($request->session()->has('lastToken') && $request->session()->get('lastToken') === $submissionToken) {
            return response()->json(['error' => 'Спасибо за отправку'], 422);
        }
        $request->session()->put('lastToken', $submissionToken);
        try {
            $userId = auth()->id();
            $answersData = $request->input('answers', []);

            foreach ($answersData as $questionId => $selectedAnswers) {
                foreach ($selectedAnswers as $answerId) {
                    UserAnswer::create([
                        'user_id' => $userId,
                        'question_id' => $questionId,
                        'answer_id' => $answerId
                    ]);
                }
            }

            $request->session()->put('form_submitted', true);

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
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        $isAuthenticated = Auth::check();

        $questions = Question::whereHas('slide', function ($query) use ($courseId) {
            $query->where('course_id', $courseId);
        })->with('answers')->get();

        $themes = $questions->groupBy('theme');

        $statistics = [];

        foreach ($themes as $theme => $questions) {
            $correctCount = 0;
            $answerOptionsCount = 0;  // Счетчик для общего числа вариантов ответов

            foreach ($questions as $question) {
                // Подсчет правильных ответов, как и раньше
                $correctAnswer = $question->answers->firstWhere('is_correct', true);
                if ($correctAnswer) {
                    $userCorrectAnswerCount = UserAnswer::where('question_id', $question->id)
                        ->where('user_id', $userId)
                        ->where('answer_id', $correctAnswer->id)
                        ->count();

                    $correctCount += $userCorrectAnswerCount;
                }

                // Подсчет всех возможных ответов для вопроса
                $answerOptionsCount += $question->answers->count();
            }
            $answerOptionsCount = min($answerOptionsCount, 10);

//            dd($answerOptionsCount);
            // Добавление статистики по теме
            $statistics[] = [
                'theme' => $theme,
                'correctCount' => $correctCount,
                'answerOptionsCount' => $answerOptionsCount  // Общее число вариантов ответов
            ];
        }

        return view('user_statistics', [
            'url' => url()->previous(),
            'isAdmin' => $isAdmin,
            'is_authenticated' =>$isAuthenticated,
            'statistics' => $statistics,
            'userId' => $userId,
            'courseId' => $courseId,
        ]);
    }
}
