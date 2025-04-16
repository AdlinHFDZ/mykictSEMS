<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'course_name',
        'course_id',
        'section',
        'questions',
    ];
}
