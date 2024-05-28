<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    protected $fillable = ['related_id', 'file_path', 'file_type', 'file_name'];

    use HasFactory;
}
