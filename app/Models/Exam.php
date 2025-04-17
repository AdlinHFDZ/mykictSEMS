<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CCAssignment;


class Exam extends Model
{
    protected $fillable = [
        'course_name',
        'course_code', // ✅ this is the correct one!
        'section',
        'questions',
        'tos',
        'status',
        'created_by',
    ];


    protected $casts = [
        'tos' => 'array',
    ];

    public function ccAssignment()
    {
        return $this->hasOne(CCAssignment::class);
    }


}


