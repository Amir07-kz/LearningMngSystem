@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Slide</h2>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('slides.update', $slide) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $slide->title) }}" required>
            </div>

            <div class="form-group">
                <label for="subtitle">Subtitle (Optional)</label>
                <input type="text" class="form-control" id="subtitle" name="subtitle" value="{{ old('subtitle', $slide->subtitle) }}">
            </div>

            <div class="form-group">
                <label for="button_text">Button Text</label>
                <input type="text" class="form-control" id="button_text" name="button_text" value="{{ old('button_text', $slide->button_text) }}">
            </div>

            <div class="form-group">
                <label for="audio">Audio (Optional)</label>
                <input type="file" class="form-control-file" id="audio" name="audio">
                @if($slide->audio_path)
                    <audio controls>
                        <source src="{{ asset('storage/' . $slide->audio_path) }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                @endif
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="has_exit_button" name="has_exit_button" {{ $slide->has_exit_button ? 'checked' : '' }}>
                <label class="form-check-label" for="has_exit_button">Exit Button</label>
            </div>

            <button type="submit" class="btn btn-primary">Update Slide</button>
        </form>
    </div>
@endsection
