<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamUser extends Model
{
    //
    protected $fillable = [
        'user_id',
        'exam_id',
        'is_finished',
    ];
}
