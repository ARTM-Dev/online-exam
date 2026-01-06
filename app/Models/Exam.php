<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Exam extends Model
{
    protected $fillable = [
        'name',
        'course_id',
        'q_number',
        'start_time',
        'end_time',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class , 'exam_questions');
    }
}
