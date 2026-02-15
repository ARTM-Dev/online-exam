<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {

            for ($i = 1; $i <= 10; $i++) {

                Question::create([
                    'course_id' => $course->id,
                    'question_text' => "سوال {$i} مربوط به دوره {$course->name} چیست؟",
                    'option_1' => 'گزینه اول',
                    'option_2' => 'گزینه دوم',
                    'option_3' => 'گزینه سوم',
                    'option_4' => 'گزینه چهارم',
                    'answer' => rand(1, 4),
                ]);

            }

        }
    }
}
