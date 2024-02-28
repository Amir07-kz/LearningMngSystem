<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class Slide extends Model
{
    protected $fillable = ['course_id', 'title', 'subtitle', 'button_text', 'audio_path', 'has_exit_button'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
