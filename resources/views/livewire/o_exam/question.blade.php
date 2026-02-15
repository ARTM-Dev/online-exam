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
        if (auth()->user()->role !== 'admin'){
            abort(403);
        }
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

    public function resetNewQuestionForm(): void
    {
        $this->reset([
            'course_id',
            'question_text',
            'option_1',
            'option_2',
            'option_3',
            'option_4',
            'answer',
        ]);
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
    <flux:main class="p-6 space-y-6">

        <div class="flex justify-between items-center">
            <flux:heading size="lg">مدیریت سوالات</flux:heading>

            <flux:modal.trigger name="New_Question">
                <flux:button color="green" variant="primary">
                    + افزودن سوال
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($questions as $question)
                <flux:card class="space-y-3">

                    <div class="flex justify-between items-center">
                        <flux:heading size="md">
                            سوال #{{ $question->id }}
                        </flux:heading>

                        <flux:badge color="blue">
                            {{ $question->course->name ?? 'نامشخص' }}
                        </flux:badge>
                    </div>

                    <flux:text size="sm">
                        {{ $question->question_text }}
                    </flux:text>

                    <flux:separator />

                    <ul class="text-sm space-y-1">
                        <li><strong>A:</strong> {{ $question->option_1 }}</li>
                        <li><strong>B:</strong> {{ $question->option_2 }}</li>
                        <li><strong>C:</strong> {{ $question->option_3 }}</li>
                        <li><strong>D:</strong> {{ $question->option_4 }}</li>
                    </ul>

                    <flux:callout color="green" size="sm">
                        پاسخ صحیح:
                        {{ ['A','B','C','D'][$question->answer - 1] }}
                    </flux:callout>

                    <flux:separator />

                    <div class="flex justify-between">
                        <flux:modal.trigger :name="'delete_question'.$question->id">
                            <flux:button
                                size="xs"
                                color="red"
                                variant="outline"
                                wire:click="deleting_id({{ $question->id }})"
                            >
                                حذف
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:modal.trigger :name="'edit_question'.$question->id">
                            <flux:button
                                size="xs"
                                variant="outline"
                                wire:click="edit_question({{ $question->id }})"
                            >
                                ویرایش
                            </flux:button>
                        </flux:modal.trigger>
                    </div>

                </flux:card>

                <flux:modal :name="'delete_question'.$question->id" class="md:w-96">
                    <div class="space-y-6">
                        <flux:heading size="lg">
                            حذف سوال
                        </flux:heading>

                        <form wire:submit.prevent="delete_question">
                            <div class="flex justify-end gap-2">
                                <flux:button type="submit" color="red" variant="primary">
                                    حذف
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </flux:modal>

                <flux:modal :name="'edit_question'.$question->id" class="md:w-96" :dismissible="false">
                    <form wire:submit.prevent="update_question" class="space-y-4">

                        <flux:heading size="lg">ویرایش سوال</flux:heading>

                        <flux:select wire:model="edit_course_id" label="دوره">
                            <flux:select.option value="">انتخاب دوره</flux:select.option>
                            @foreach($courses as $course)
                                <flux:select.option value="{{ $course->id }}">
                                    {{ $course->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model="edit_question_text" label="متن سوال"/>
                        <flux:input wire:model="edit_option_1" label="گزینه A"/>
                        <flux:input wire:model="edit_option_2" label="گزینه B"/>
                        <flux:input wire:model="edit_option_3" label="گزینه C"/>
                        <flux:input wire:model="edit_option_4" label="گزینه D"/>

                        <flux:select wire:model="edit_answer" label="پاسخ صحیح">
                            <flux:select.option value="">انتخاب پاسخ</flux:select.option>
                            <flux:select.option value="1">A</flux:select.option>
                            <flux:select.option value="2">B</flux:select.option>
                            <flux:select.option value="3">C</flux:select.option>
                            <flux:select.option value="4">D</flux:select.option>
                        </flux:select>

                        <div class="flex justify-end">
                            <flux:button type="submit" variant="primary">
                                ذخیره تغییرات
                            </flux:button>
                        </div>

                    </form>
                </flux:modal>
            @endforeach
        </div>

        <flux:modal
            name="New_Question"
            class="md:w-96"
            x-on:close="$wire.resetNewQuestionForm()"
        >
            <form wire:submit.prevent="save" class="space-y-4">

                <flux:heading size="lg">افزودن سوال جدید</flux:heading>

                <flux:select wire:model="course_id" label="دوره">
                    <flux:select.option value="">انتخاب دوره</flux:select.option>
                    @foreach($courses as $course)
                        <flux:select.option value="{{ $course->id }}">
                            {{ $course->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="question_text" label="متن سوال"/>
                <flux:input wire:model="option_1" label="گزینه A"/>
                <flux:input wire:model="option_2" label="گزینه B"/>
                <flux:input wire:model="option_3" label="گزینه C"/>
                <flux:input wire:model="option_4" label="گزینه D"/>

                <flux:select wire:model="answer" label="پاسخ صحیح">
                    <flux:select.option value="">انتخاب پاسخ</flux:select.option>
                    <flux:select.option value="1">A</flux:select.option>
                    <flux:select.option value="2">B</flux:select.option>
                    <flux:select.option value="3">C</flux:select.option>
                    <flux:select.option value="4">D</flux:select.option>
                </flux:select>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary">
                        ذخیره
                    </flux:button>
                </div>

            </form>
        </flux:modal>

    </flux:main>
</div>
