<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'exam_id',
        'user_id',
        'score',

    ];
}
