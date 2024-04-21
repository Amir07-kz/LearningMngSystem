<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionTheme extends Model
{
    use HasFactory;

    protected $table = 'question_themes';

    protected $fillable = [
        'name',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
