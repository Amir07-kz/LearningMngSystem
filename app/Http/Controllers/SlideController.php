<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
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
            // ������� ������ ����, ���� �� ����������
            if ($slide->audio_path && \Storage::disk('public')->exists($slide->audio_path)) {
                \Storage::disk('public')->delete($slide->audio_path);
            }

            // ��������� ����� ���� � �������� ���� � ����� � ������
            $data['audio_path'] = $request->file('audio')->store('audios', 'public');
        }

        $slide->update($data);

        return back()->with('success', 'Slide updated successfully.');
    }

    public function create($courseId)
    {
        // ����� $courseId - ��� ������������� �����, � �������� �� ������ �������� ������.
        // �� ������ ������������ ���� ID, ����� �������� ������ ����� � �������������, ���� ��� ����������.
        return view('slides.create', compact('courseId'));
    }

}
