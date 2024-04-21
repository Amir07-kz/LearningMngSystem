<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['text', 'correct_answer_id', 'theme'];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function correctAnswer()
    {
        return $this->belongsTo(Answer::class, 'correct_answer_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}
