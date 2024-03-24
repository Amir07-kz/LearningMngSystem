<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
//    public function index($courseId)
//    {
//        $slides = Slide::where('course_id', $courseId)->orderBy('slide_number')->get();
//        return view('slide_list', ['courseId' => $courseId, 'slides' => $slides]);
//    }
    public function index($courseId)
    {
        $slides = Slide::where('course_id', $courseId)->orderBy('slide_number')->get();
        return view('create_slides', ['courseId' => $courseId, 'slides' => $slides]);
    }

    public function firstSlide($courseId)
    {
        $firstSlide = Slide::where('course_id', $courseId)
            ->orderBy('slide_number', 'asc')
            ->first();

        if (!$firstSlide) {
            return view('slide_list', ['courseId' => $courseId]);
        }

        return redirect()->to('/courses/' . $courseId . '/slide/' . $firstSlide->slide_number);
    }
    public function show($courseId, $slideNumber)
    {
        $slide = Slide::where('course_id', $courseId)->where('slide_number', $slideNumber)->firstOrFail();
        $slides = Slide::where('course_id', $courseId)->orderBy('slide_number')->get();
        return view('slides_show', [
            'slide' => $slide,
            'slides' => $slides,
            'courseId' => $courseId
        ]);
    }
    public function store(Request $request, $courseId)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $slide = new Slide();
        $slide->fill($data);
        $slide->course_id = $courseId;
        $slide->slide_number = Slide::where('course_id', $courseId)->max('slide_number') + 1;
        $slide->save();

        return redirect()->back();
    }

    public function update(Request $request, $courseId, $slideId) {

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $slide = Slide::where('course_id', $courseId)->where('slide_number', $slideId)->first();

        if ($slide) {
            $slide->title = $validatedData['title'];
            $slide->description = $validatedData['description'];
            $slide->save();
        }

        return redirect()->back();
    }

    public function remove($courseId, $slideId) {

        Slide::where('course_id', $courseId)->where('slide_number', $slideId)->delete();
        $previousSlide = Slide::where('course_id', $courseId)
            ->where('slide_number', '<', $slideId)
            ->orderBy('slide_number', 'desc')
            ->first();

        if ($previousSlide) {
            return redirect()->to('/courses/' . $courseId . '/slide/' . $previousSlide->slide_number);
        }

        return redirect()->back();
    }
}
