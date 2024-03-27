<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlideDescription extends Model
{
    protected $fillable = ['description', 'slide_id'];
}
