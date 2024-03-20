<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    public function index($courseId)
    {
        $slides = Slide::where('course_id', $courseId)->orderBy('slide_number')->get();
        return view('create_slides', ['courseId' => $courseId, 'slides' => $slides]);
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
            'position' => 'required|integer',
        ]);

        $slide = Slide::where('course_id', $courseId)->where('slide_number', $slideId)->first();

        if ($slide) {
            $slide->title = $validatedData['title'];
            $slide->description = $validatedData['description'];
            $slide->position = $validatedData['position'];
            $slide->save();
        }

        return redirect()->back();
    }

    public function remove($courseId, $slideId) {
        $slide = Slide::where('course_id', $courseId)->where('slide_number', $slideId)->delete();
        return redirect()->back();
    }
}
