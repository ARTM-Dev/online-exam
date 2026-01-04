<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    protected $fillable = [
        'course_id',
        'question_text',
        'option_1',
        'option_2',
        'option_3',
        'option_4',
        'answer',
    ];

    public function course() : BelongsTo
    {
        return $this->belongsTo(Course::class);
    }


    public function Exam():BelongsToMany
    {
        return $this->belongsToMany(Exam::class);
    }
}
