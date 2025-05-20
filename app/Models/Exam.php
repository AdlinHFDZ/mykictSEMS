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
        'approved_by', // Make sure to add this
        'status',
        'semester_id',
        'course_id',
    ];

    protected $casts = [
        'tos' => 'array',
        'questions' => 'array',
    ];

    // Relationship: Assigned Course Coordinator
    public function ccAssignment()
    {
        return $this->hasOne(CCAssignment::class, 'exam_id');
    }

    // Relationship: Assigned Vetters
    public function vetterAssignments()
    {
        return $this->hasMany(VetterAssignment::class, 'exam_id');
    }

    // Relationship: Semester
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    // Relationship: Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relationship: User who created the exam (could be CC)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship: User who approved the exam (HOD)
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
