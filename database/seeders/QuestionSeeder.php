<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Course;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $php_questions = [
            [
                'question_text' => 'کدام یک از موارد زیر نوع داده عدد صحیح در PHP است؟',
                'option_1' => 'int',
                'option_2' => 'string',
                'option_3' => 'bool',
                'option_4' => 'array',
                'answer' => 1
            ],
            [
                'question_text' => 'برای شروع بلاک کد در PHP از چه علامتی استفاده می‌کنیم؟',
                'option_1' => '{}',
                'option_2' => '[]',
                'option_3' => '()',
                'option_4' => '<>',
                'answer' => 1
            ],
            [
                'question_text' => 'کدام تابع برای چاپ متن در PHP استفاده می‌شود؟',
                'option_1' => 'echo',
                'option_2' => 'print_r',
                'option_3' => 'var_dump',
                'option_4' => 'printf',
                'answer' => 1
            ],
            [
                'question_text' => 'چگونه می‌توان یک آرایه در PHP ایجاد کرد؟',
                'option_1' => '$arr = {}',
                'option_2' => '$arr = []',
                'option_3' => '$arr = ()',
                'option_4' => '$arr = <>',
                'answer' => 2
            ],
            [
                'question_text' => 'برای شروع یک بلاک شرطی if از چه کدی استفاده می‌کنیم؟',
                'option_1' => 'if(condition)',
                'option_2' => 'if condition',
                'option_3' => 'if {condition}',
                'option_4' => 'if:condition',
                'answer' => 1
            ],
            [
                'question_text' => 'کدام یک از موارد زیر روش صحیح تعریف تابع در PHP است؟',
                'option_1' => 'function myFunc() {}',
                'option_2' => 'func myFunc() {}',
                'option_3' => 'define myFunc() {}',
                'option_4' => 'method myFunc() {}',
                'answer' => 1
            ],
            [
                'question_text' => 'برای اتصال به پایگاه داده MySQL در PHP از کدام تابع استفاده می‌شود؟',
                'option_1' => 'mysqli_connect',
                'option_2' => 'mysql_open',
                'option_3' => 'db_connect',
                'option_4' => 'connect_mysql',
                'answer' => 1
            ],
            [
                'question_text' => 'کدام علامت برای شروع یک کامنت تک خطی در PHP است؟',
                'option_1' => '//',
                'option_2' => '/*',
                'option_3' => '#',
                'option_4' => '--',
                'answer' => 1
            ],
            [
                'question_text' => 'متغیرها در PHP با چه علامتی شروع می‌شوند؟',
                'option_1' => '&',
                'option_2' => '$',
                'option_3' => '#',
                'option_4' => '%',
                'answer' => 2
            ],
            [
                'question_text' => 'کدام تابع برای بررسی وجود کلید در آرایه در PHP استفاده می‌شود؟',
                'option_1' => 'array_key_exists',
                'option_2' => 'isset_key',
                'option_3' => 'in_array',
                'option_4' => 'key_exists',
                'answer' => 1
            ],
        ];

        $js_questions = [
            [
                'question_text' => 'کدام یک از موارد زیر نوع داده عدد صحیح در JavaScript است؟',
                'option_1' => 'int',
                'option_2' => 'Number',
                'option_3' => 'String',
                'option_4' => 'Boolean',
                'answer' => 2
            ],
            [
                'question_text' => 'برای تعریف متغیر در JavaScript از کدام کلمه کلیدی استفاده می‌کنیم؟',
                'option_1' => 'var',
                'option_2' => 'let',
                'option_3' => 'const',
                'option_4' => 'همه موارد بالا',
                'answer' => 4
            ],
            [
                'question_text' => 'کدام روش برای چاپ در کنسول JavaScript استفاده می‌شود؟',
                'option_1' => 'console.log()',
                'option_2' => 'print()',
                'option_3' => 'echo',
                'option_4' => 'alert()',
                'answer' => 1
            ],
            [
                'question_text' => 'کدام یک از موارد زیر آرایه صحیح در JavaScript است؟',
                'option_1' => 'let arr = {}',
                'option_2' => 'let arr = []',
                'option_3' => 'let arr = ()',
                'option_4' => 'let arr = <>',
                'answer' => 2
            ],
            [
                'question_text' => 'برای شرطی if در JavaScript از چه کدی استفاده می‌کنیم؟',
                'option_1' => 'if (condition) {}',
                'option_2' => 'if condition {}',
                'option_3' => 'if:condition {}',
                'option_4' => 'if [condition] {}',
                'answer' => 1
            ],
            [
                'question_text' => 'کدام روش برای تعریف تابع در JavaScript صحیح است؟',
                'option_1' => 'function myFunc() {}',
                'option_2' => 'func myFunc() {}',
                'option_3' => 'define myFunc() {}',
                'option_4' => 'method myFunc() {}',
                'answer' => 1
            ],
            [
                'question_text' => 'برای بررسی طول آرایه در JavaScript از کدام استفاده می‌کنیم؟',
                'option_1' => 'arr.length',
                'option_2' => 'arr.size',
                'option_3' => 'arr.count',
                'option_4' => 'length(arr)',
                'answer' => 1
            ],
            [
                'question_text' => 'کدام علامت برای کامنت تک خطی در JavaScript است؟',
                'option_1' => '//',
                'option_2' => '/* */',
                'option_3' => '#',
                'option_4' => '<!-- -->',
                'answer' => 1
            ],
            [
                'question_text' => 'کدام روش برای حلقه for صحیح است؟',
                'option_1' => 'for (let i=0; i<5; i++) {}',
                'option_2' => 'for i=0; i<5; i++ {}',
                'option_3' => 'for (i in 5) {}',
                'option_4' => 'foreach(i=0;i<5;i++) {}',
                'answer' => 1
            ],
            [
                'question_text' => 'برای تبدیل رشته به عدد در JavaScript از چه روشی استفاده می‌کنیم؟',
                'option_1' => 'Number(str)',
                'option_2' => 'parseInt(str)',
                'option_3' => 'parseFloat(str)',
                'option_4' => 'همه موارد بالا',
                'answer' => 4
            ],
        ];

        $courses = Course::all();

        foreach ($courses as $course) {
            if ($course->name == 'برنامه نویسی PHP') {
                foreach ($php_questions as $q) {
                    $q['course_id'] = $course->id;
                    Question::create($q);
                }
            } else {
                foreach ($js_questions as $q) {
                    $q['course_id'] = $course->id;
                    Question::create($q);
                }
            }
        }
    }
}
