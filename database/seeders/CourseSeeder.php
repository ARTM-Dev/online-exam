<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['name' => 'برنامه نویسی PHP'],
            ['name' => 'برنامه نویسی JavaScript'],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
