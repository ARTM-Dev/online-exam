<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{


    public function Exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
    public function Questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
