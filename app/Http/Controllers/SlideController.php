<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
//    public function index($courseId)
//    {
//        return view('create_slides', ['courseId' => $courseId]);
//    }
    public function index($courseId)
    {
        $slides = Slide::where('course_id', $courseId)->orderBy('position')->get();
        return view('create_slides', ['courseId' => $courseId, 'slides' => $slides]);
    }


    public function edit(Slide $slide)
    {
        return view('slides.edit', compact('slide'));
    }

    public function update(Request $request, Slide $slide) {
        $data = $request->validate([
            'title' => 'required',
            'subtitle' => 'sometimes|nullable',
            'button_text' => 'sometimes|nullable',
            'audio' => 'sometimes|file|mimes:mp3,wav',
            'has_exit_button' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('audio')) {
            // Удалите старый файл, если он существует
            if ($slide->audio_path && \Storage::disk('public')->exists($slide->audio_path)) {
                \Storage::disk('public')->delete($slide->audio_path);
            }

            // Сохраните новый файл и обновите путь к файлу в данных
            $data['audio_path'] = $request->file('audio')->store('audios', 'public');
        }

        $slide->update($data);

        return back()->with('success', 'Slide updated successfully.');
    }
    public function store(Request $request, $courseId)
    {
//        dump($courseId);
//        dd($request);
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $slide = new Slide();
        $slide->fill($data);
        $slide->course_id = $courseId;
        $slide->save();

        return redirect()->route('slide.create', $courseId)->with('success', 'Slide created successfully.');
    }

}
