<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VetterAssignment extends Model
{
    protected $fillable = ['exam_id', 'user_id'];

    // 👉 Add this:
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
