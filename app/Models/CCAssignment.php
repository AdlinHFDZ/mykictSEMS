<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CCAssignment extends Model
{
    use HasFactory;

    protected $table = 'cc_assignments'; // ✅ FIX here

    protected $fillable = [
        'exam_id',
        'user_id',
    ];
}
