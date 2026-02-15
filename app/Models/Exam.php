<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;



class Exam extends Model
{
    protected $fillable = [
        'name',
        'course_id',
        'q_number',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        $now = now();

        if ($now->lt($this->start_time)) {
            return 'upcoming';
        }

        if ($now->between($this->start_time, $this->end_time)) {
            return 'active';
        }

        return 'closed';
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions');
    }

    public function examAnswers()
    {
        return $this->hasMany(ExamUserAnswer::class);
    }
}
