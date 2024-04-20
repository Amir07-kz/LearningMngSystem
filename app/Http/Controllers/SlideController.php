<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\MediaFile;
use App\Models\Question;
use App\Models\Slide;
use App\Models\SlideDescription;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SlideController
{
    public function index($courseId)
    {
        $slides = Slide::where('course_id', $courseId)->orderBy('slide_number')->get();
        return view('create_slides', ['courseId' => $courseId, 'slides' => $slides]);
    }

    public function firstSlide($courseId)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $is_admin = $user->isAdmin();
        } else {
            $is_admin = false;
        }

        $firstSlide = Slide::where('course_id', $courseId)
            ->orderBy('slide_number', 'asc')
            ->first();

        if (!$firstSlide) {
            return view('slide_list', ['courseId' => $courseId, 'is_admin'=> $is_admin]);
        }

        return redirect()->to('/courses/' . $courseId . '/slide/' . $firstSlide->slide_number);
    }
    public function show($courseId, $slideNumber)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $is_admin = $user->isAdmin();
        } else {
            $is_admin = false;
        }

        $slide = Slide::with(['descriptions', 'mediaFiles', 'questions.answers'])
            ->where('course_id', $courseId)
            ->where('slide_number', $slideNumber)
            ->firstOrFail();

        $slides = Slide::where('course_id', $courseId)
            ->orderBy('slide_number')
            ->get();

        foreach ($slide->questions as $question) {
            $question->userAnswer = UserAnswer::where('user_id', $user->id)
                ->where('question_id', $question->id)
                ->first();
        }

        return view('slides_show', [
            'is_admin' => $is_admin,
            'slide' => $slide,
            'slides' => $slides,
            'courseId' => $courseId
        ]);
    }
    public function store(Request $request, $courseId)
    {
        $data = $request->validate([
            'title' => 'string|max:255',
        ]);

        $slide = new Slide();
        $slide->fill($data);
        $slide->course_id = $courseId;
        $slide->slide_number = Slide::where('course_id', $courseId)->max('slide_number') + 1;
        $slide->save();

        return redirect()->back();
    }

    public function update(Request $request, $courseId, $slideId)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'new_description.*' => 'nullable',
            'descriptions.*' => 'required',
            'question_text' => 'nullable|max:255',
            'answers.*' => 'nullable|string|max:255',
            'correct_answers.*' => 'nullable',

        ]);

        $slide = Slide::with('descriptions')->where('course_id', $courseId)->where('slide_number', $slideId)->firstOrFail();
        $slide->title = $validatedData['title'];

        if ($request->filled('question_text')) {
            $question = new Question([
                'text' => $validatedData['question_text']
            ]);

            $slide->questions()->save($question);

            if ($request->has('answers')) {
                $answers = $request->input('answers');
                $correctAnswers = $request->input('correct_answers', []);
                foreach ($answers as $index => $answerText) {
                    if (!empty($answerText)) {
                        $answer = new Answer([
                            'text' => $answerText,
                            'is_correct' => in_array($index + 1, $correctAnswers),
                        ]);

                        $question->answers()->save($answer);
                    }
                }
            }
        }

        if ($request->has('questions')) {
            foreach ($request->input('questions') as $questionId => $questionData) {
                $question = Question::find($questionId);
                if ($question) {
                    $question->text = $questionData['text'];
                    $question->save();

                    if (isset($questionData['answers'])) {
                        foreach ($questionData['answers'] as $answerId => $answerData) {
                            $answer = Answer::find($answerId);
                            if ($answer) {
                                $answer->text = $answerData['text'];
                                $answer->is_correct = isset($answerData['is_correct']); // Проверьте, установлен ли флаг is_correct
                                $answer->save();
                            }
                        }
                    }
                }
            }
        }

        if ($request->hasFile('image')) {
            $mediaFile = $request->file('image');
            $filePath = $mediaFile->store('image', 'public');
            $fileType = $mediaFile->getMimeType();

            MediaFile::create([
                'related_id' => $slide->id,
                'file_path'  => $filePath,
                'file_type'  => $fileType
            ]);
        }

        if (isset($validatedData['descriptions'])) {
            foreach ($validatedData['descriptions'] as $descriptionId => $descriptionText) {
                $description = SlideDescription::find($descriptionId);
                if ($description) {
                    $description->description = $descriptionText;
                    $description->save();
                }
            }
        }

        if (isset($validatedData['new_description'])) {
            foreach ($validatedData['new_description'] as $descriptionText) {
                if (!empty($descriptionText)) {
                    $slide->descriptions()->create(['description' => $descriptionText]);
                }
            }
        }

        $slide->save();
        return redirect()->back();
    }

    public function remove($courseId, $slideId) {
        $slide = Slide::where('course_id', $courseId)->where('slide_number', $slideId)->delete();

        Slide::where('course_id', $courseId)->where('slide_number', $slideId)->delete();
        $previousSlide = Slide::where('course_id', $courseId)
            ->where('slide_number', '<', $slideId)
            ->orderBy('slide_number', 'desc')
            ->first();

        if ($previousSlide) {
            return redirect()->to('/courses/' . $courseId . '/slide/' . $previousSlide->slide_number);
        }

        return redirect()->route('course.slideList', ['course' => $courseId]);
    }

    public function slideContentRemove($courseId, $slideId, $descriptionId) {
        $description = SlideDescription::find($descriptionId);
        if ($description) {
            $description->delete();
        }
        return redirect()->back();
    }

    public function deleteMedia($id)
    {
        $mediaFile = MediaFile::findOrFail($id);
        Storage::disk('public')->delete($mediaFile->file_path);
        $mediaFile->delete();
        return response()->json(['success' => true]);
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::findOrFail($questionId);
        $question->answers()->delete();
        $question->delete();

        return back()->with('success', 'Вопрос успешно удален.');
    }
}
