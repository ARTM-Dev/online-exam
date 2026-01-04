<?php

use Livewire\Volt\Component;
use App\Models\Question;
use App\Models\Course;

new class extends Component {

    public $questions;
    public $courses;

    public $course_id = '';
    public $question_text = '';
    public $option_1 = '';
    public $option_2 = '';
    public $option_3 = '';
    public $option_4 = '';
    public $answer = '';

    public $edit_id = '';
    public $edit_course_id = '';
    public $edit_question_text = '';
    public $edit_option_1 = '';
    public $edit_option_2 = '';
    public $edit_option_3 = '';
    public $edit_option_4 = '';
    public $edit_answer = '';

    public $delete_id = '';

    public function mount(): void
    {
        $this->get_questions();
        $this->get_courses();
    }

    public function get_questions(): void
    {
        $this->questions = Question::with('course')->get();
    }

    public function get_courses(): void
    {
        $this->courses = Course::all();
    }

    public function save()
    {
        $this->validate([
            'course_id' => 'required|exists:courses,id',
            'question_text' => 'required|string',
            'option_1' => 'required|string',
            'option_2' => 'required|string',
            'option_3' => 'required|string',
            'option_4' => 'required|string',
            'answer' => 'required|in:1,2,3,4',
        ]);

        Question::create([
            'course_id' => $this->course_id,
            'question_text' => $this->question_text,
            'option_1' => $this->option_1,
            'option_2' => $this->option_2,
            'option_3' => $this->option_3,
            'option_4' => $this->option_4,
            'answer' => $this->answer,
        ]);

        \Flux::modal('New_Question')->close();
        $this->get_questions();
        $this->reset(['course_id','question_text','option_1','option_2','option_3','option_4','answer']);
    }


    public function deleting_id($id)
    {
        $this->delete_id = $id;
    }

    public function delete_question()
    {
        Question::find($this->delete_id)?->delete();
        Flux::modal('delete_question'.$this->delete_id)->close();
        $this->get_questions();
        $this->delete_id = '';
    }

    public function edit_question($id)
    {
        $this->edit_id = $id;
        $q = Question::find($id);

        $this->edit_course_id = $q->course_id;
        $this->edit_question_text = $q->question_text;
        $this->edit_option_1 = $q->option_1;
        $this->edit_option_2 = $q->option_2;
        $this->edit_option_3 = $q->option_3;
        $this->edit_option_4 = $q->option_4;
        $this->edit_answer = $q->answer;
    }

    public function update_question()
    {

        $this->validate([
            'edit_course_id' => 'required|exists:courses,id',
            'edit_question_text' => 'required|string',
            'edit_option_1' => 'required|string',
            'edit_option_2' => 'required|string',
            'edit_option_3' => 'required|string',
            'edit_option_4' => 'required|string',
            'edit_answer' => 'required|in:1,2,3,4',
        ]);

        $q = Question::find($this->edit_id);
        $q->update([
            'course_id' => $this->edit_course_id,
            'question_text' => $this->edit_question_text,
            'option_1' => $this->edit_option_1,
            'option_2' => $this->edit_option_2,
            'option_3' => $this->edit_option_3,
            'option_4' => $this->edit_option_4,
            'answer' => $this->edit_answer,
        ]);

        Flux::modal('edit_question'.$this->edit_id)->close();
        $this->get_questions();

        $this->reset([
            'edit_id','edit_course_id','edit_question_text','edit_option_1','edit_option_2','edit_option_3','edit_option_4','edit_answer'
        ]);
    }

};

?>
<div>

    <flux:main class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <flux:modal.trigger name="New_Question">
            <flux:button class="mt-4">افزودن سوال جدید</flux:button>
        </flux:modal.trigger>

        <flux:modal name="New_Question" class="md:w-96">
            <form wire:submit.prevent="save">
                <div class="space-y-6">
                    <flux:heading size="lg">افزودن سوال جدید</flux:heading>

                    <flux:select wire:model="course_id" label="دوره">
                        <flux:select.option value="" disabled selected>انتخاب دوره</flux:select.option>
                        @foreach($courses as $course)
                            <flux:select.option value="{{ $course->id }}">{{ $course->name }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="question_text" label="متن سوال"/>
                    <flux:input wire:model="option_1" label="گزینه A"/>
                    <flux:input wire:model="option_2" label="گزینه B"/>
                    <flux:input wire:model="option_3" label="گزینه C"/>
                    <flux:input wire:model="option_4" label="گزینه D"/>
                    <flux:select wire:model="answer" label="پاسخ صحیح">
                        <flux:select.option value="" disabled selected>انتخاب پاسخ صحیح</flux:select.option>
                        <flux:select.option value="1">A</flux:select.option>
                        <flux:select.option value="2">B</flux:select.option>
                        <flux:select.option value="3">C</flux:select.option>
                        <flux:select.option value="4">D</flux:select.option>
                    </flux:select>

                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary">ذخیره</flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>
        <flux:spacer />


        @foreach($questions as $question)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 hover:shadow-xl transition">
                <h2 class="text-lg font-bold mb-2 text-purple-600">سوال #{{ $question->id }}</h2>
                <p><span class="font-semibold">دوره:</span> {{ $question->course->name ?? 'نامشخص' }}</p>
                <p><span class="font-semibold">متن سوال:</span> {{ $question->question_text }}</p>

                <p><span class="font-semibold">گزینه‌ها:</span></p>
                <ul class="ml-4">
                    <li>A: {{ $question->option_1 }}</li>
                    <li>B: {{ $question->option_2 }}</li>
                    <li>C: {{ $question->option_3 }}</li>
                    <li>D: {{ $question->option_4 }}</li>
                </ul>

                <p><span class="font-semibold">پاسخ صحیح:</span> {{ ['A','B','C','D'][$question->answer - 1] }}</p>

                <flux:modal.trigger :name="'delete_question'.$question->id">
                    <flux:button wire:click="deleting_id({{ $question->id }})" variant="danger">حذف سوال</flux:button>
                </flux:modal.trigger>

                <flux:modal :name="'delete_question'.$question->id" class="md:w-96">
                    <div class="space-y-6">
                        <flux:heading size="lg">حذف سوال #{{ $question->id }}</flux:heading>
                        <form wire:submit="delete_question()" class="space-y-6">
                            <div class="flex justify-end">
                                <flux:button type="submit" variant="primary">حذف</flux:button>
                            </div>
                        </form>
                    </div>
                </flux:modal>

                <flux:modal.trigger :name="'edit_question'.$question->id">
                    <flux:button wire:click="edit_question({{ $question->id }})" variant="primary" class="mt-2">ویرایش سوال</flux:button>
                </flux:modal.trigger>

                <flux:modal :name="'edit_question'.$question->id" class="md:w-96" :dismissible="false">
                    <div class="space-y-6">
                        <flux:heading size="lg">ویرایش سوال</flux:heading>

                        <form wire:submit="update_question()" class="space-y-6">

                            <flux:select wire:model="edit_course_id" label="دوره">
                                @foreach($courses as $course)
                                    <flux:select.option value="{{ $course->id }}">{{ $course->name }}</flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:input wire:model="edit_question_text" label="متن سوال"/>
                            <flux:input wire:model="edit_option_1" label="گزینه A"/>
                            <flux:input wire:model="edit_option_2" label="گزینه B"/>
                            <flux:input wire:model="edit_option_3" label="گزینه C"/>
                            <flux:input wire:model="edit_option_4" label="گزینه D"/>
                            <flux:select wire:model="edit_answer" label="پاسخ صحیح">
                                <flux:select.option value="1">A</flux:select.option>
                                <flux:select.option value="2">B</flux:select.option>
                                <flux:select.option value="3">C</flux:select.option>
                                <flux:select.option value="4">D</flux:select.option>
                            </flux:select>

                            <div class="flex justify-end">
                                <flux:button type="submit" variant="primary">ذخیره تغییرات</flux:button>
                            </div>
                        </form>
                    </div>
                </flux:modal>

            </div>
        @endforeach

    </flux:main>
</div>
