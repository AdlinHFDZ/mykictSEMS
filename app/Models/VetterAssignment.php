<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VetterAssignment extends Model
{
    protected $fillable = ['exam_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
