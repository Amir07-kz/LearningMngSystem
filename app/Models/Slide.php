<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class Slide extends Model
{
    protected $fillable = ['id', 'course_id', 'title', 'description', 'created_at', 'updated_at', 'slide_number'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function descriptions()
    {
        return $this->hasMany(SlideDescription::class);
    }

    public function mediaFiles()
    {
        return $this->hasMany(MediaFile::class, 'related_id');
    }
}
