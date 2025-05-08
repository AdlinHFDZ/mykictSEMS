<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_name',
        'course_code',
        'section',
        'questions',
        'tos',
        'created_by',
        'status',
        'semester_id',
        'course_id',
    ];

    protected $casts = [
        'tos' => 'array',
        'questions' => 'array',
    ];

    public function ccAssignment()
    {
        return $this->hasOne(CCAssignment::class);
    }

    public function vetterAssignments()
    {
        return $this->hasMany(VetterAssignment::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    
}
