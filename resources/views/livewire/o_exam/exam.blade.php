<?php

use App\Models\Course;
use App\Models\Question;
use Livewire\Volt\Component;
use App\Models\Exam;

new class extends Component {

    public $exams;
    public $courses;

    public $course_id = '';
    public $name = '';
    public $q_number = '';
    public $start_time = '';
    public $end_time = '';


    public $edit_id = '';
    public $edit_course_id = '';
    public $edit_name = '';
    public $edit_q_number = '';
    public $edit_start_time = '';
    public $edit_end_time = '';

    public $delete_id = '';

    public function mount(): void
    {
        if (auth()->user()->role !== 'admin'){
            abort(403);
        }
        $this->get_exam();
        $this->get_course();
    }

    public function get_exam(): void
    {
        $this->exams = Exam::with('course')->get();
    }

    public function get_course(): void
    {
        $this->courses = Course::all();
    }

    public function resetNewExamForm(): void
    {
        $this->reset(['course_id', 'name', 'q_number', 'start_time', 'end_time']);
    }

    public function save(): void
    {
        $this->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string',
            'q_number' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $questionsCount = Question::where('course_id', $this->course_id)->count();

        if ($questionsCount < $this->q_number) {
            $this->addError('q_number', 'تعداد سوالات این دوره کمتر از تعداد سوالات آزمون است');
            return;
        }

        $exam = Exam::create([
            'name' => $this->name,
            'course_id' => $this->course_id,
            'q_number' => $this->q_number,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);


        $randomQuestions = Question::where('course_id', $this->course_id)
            ->inRandomOrder()
            ->take($this->q_number)
            ->pluck('id');
        $exam->questions()->attach($randomQuestions);

        Flux::modal('New_Exam')->close();
        $this->resetNewExamForm();
        $this->get_exam();
    }

    public function deleting_id($id): void
    {
        $this->delete_id = $id;
    }

    public function delete_exam(): void
    {
        $exam = Exam::find($this->delete_id);
        $exam?->questions()->detach();
        $exam?->delete();

        Flux::modal('delete_exam' . $this->delete_id)->close();
        $this->delete_id = '';
        $this->get_exam();
    }

    public function edit_exam($id): void
    {
        $exam = Exam::find($id);
        $this->edit_id = $id;
        $this->edit_course_id = $exam->course_id;
        $this->edit_name = $exam->name;
        $this->edit_q_number = $exam->q_number;
        $this->edit_start_time = optional($exam->start_time)->format('Y-m-d\TH:i');
        $this->edit_end_time = optional($exam->end_time)->format('Y-m-d\TH:i');
    }

    public function update_exam(): void
    {
        $this->validate([
            'edit_course_id' => 'required|exists:courses,id',
            'edit_name' => 'required|string',
            'edit_q_number' => 'required|integer|min:1',
            'edit_start_time' => 'required|date',
            'edit_end_time' => 'required|date|after:edit_start_time',
        ]);

        $exam = Exam::find($this->edit_id);
        $exam->update([
            'course_id' => $this->edit_course_id,
            'name' => $this->edit_name,
            'q_number' => $this->edit_q_number,
            'start_time' => $this->edit_start_time,
            'end_time' => $this->edit_end_time,
        ]);

        $randomQuestions = Question::where('course_id', $this->edit_course_id)
            ->inRandomOrder()
            ->take($this->edit_q_number)
            ->pluck('id');
        $exam->questions()->sync($randomQuestions);

        Flux::modal('edit_exam' . $this->edit_id)->close();
        $this->get_exam();

        $this->reset([
            'edit_id', 'edit_course_id', 'edit_name',
            'edit_q_number', 'edit_start_time', 'edit_end_time',
        ]);
    }
};
?>

<div>
    <flux:main class="p-6 space-y-6">

        <div class="flex justify-between items-center">
            <flux:heading size="lg">مدیریت آزمون‌ها</flux:heading>

            <flux:modal.trigger name="New_Exam">
                <flux:button color="green" variant="primary">
                    + افزودن آزمون
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($exams as $exam)
                <flux:card class="space-y-3">

                    <div class="flex justify-between items-center">
                        <flux:heading size="md">{{ $exam->name }}</flux:heading>

                        @if($exam->status === 'active')
                            <flux:badge color="green">فعال</flux:badge>
                        @elseif($exam->status === 'upcoming')
                            <flux:badge color="blue">به‌زودی</flux:badge>
                        @else
                            <flux:badge color="red">تمام‌شده</flux:badge>
                        @endif
                    </div>

                    <flux:text size="sm">دوره: {{ $exam->course->name ?? 'نامشخص' }}</flux:text>
                    <flux:text size="sm">تعداد سوالات: {{ $exam->q_number }}</flux:text>
                    <flux:text size="xs">شروع: {{ $exam->start_time }}</flux:text>
                    <flux:text size="xs">پایان: {{ $exam->end_time }}</flux:text>

                    <flux:separator />

                    <div class="flex flex-wrap gap-2">
                        <flux:button
                            size="sm"
                            variant="outline"
                            href="{{ route('exam.questions', $exam->id) }}"
                            wire:navigate
                        >سوالات</flux:button>

                        @if($exam->status === 'active')
                            <flux:button
                                size="sm"
                                color="green"
                                variant="primary"
                                href="{{ route('exam.take', $exam->id) }}"
                                wire:navigate
                            >شروع آزمون</flux:button>
                        @endif
                    </div>

                    <div class="flex justify-between pt-2">
                        <flux:modal.trigger :name="'delete_exam'.$exam->id">
                            <flux:button
                                size="xs"
                                color="red"
                                variant="outline"
                                wire:click="deleting_id({{ $exam->id }})"
                            >حذف</flux:button>
                        </flux:modal.trigger>

                        <flux:modal.trigger :name="'edit_exam'.$exam->id">
                            <flux:button
                                size="xs"
                                variant="outline"
                                wire:click="edit_exam({{ $exam->id }})"
                            >ویرایش</flux:button>
                        </flux:modal.trigger>
                    </div>
                </flux:card>

                <flux:modal :name="'delete_exam'.$exam->id" class="md:w-96">
                    <div class="space-y-6">
                        <flux:heading size="lg">حذف {{ $exam->name }}</flux:heading>
                        <form wire:submit="delete_exam()" class="space-y-6">
                            <div class="flex justify-end gap-2">
                                <flux:button type="submit" color="red" variant="primary">
                                    حذف
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </flux:modal>

                <flux:modal :name="'edit_exam'.$exam->id" class="md:w-96" :dismissible="false">
                    <form wire:submit="update_exam()" class="space-y-4">
                        <flux:heading size="lg">ویرایش آزمون</flux:heading>

                        <flux:select wire:model="edit_course_id" label="دوره">
                            <flux:select.option value="">انتخاب دوره</flux:select.option>
                            @foreach($courses as $course)
                                <flux:select.option value="{{ $course->id }}">{{ $course->name }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model="edit_name" label="نام آزمون" />
                        <flux:input wire:model="edit_q_number" label="تعداد سوالات" type="number" />
                        <flux:input wire:model="edit_start_time" label="تاریخ شروع" type="datetime-local" />
                        <flux:input wire:model="edit_end_time" label="تاریخ پایان" type="datetime-local" />

                        <div class="flex justify-end">
                            <flux:button type="submit" variant="primary">ذخیره</flux:button>
                        </div>
                    </form>
                </flux:modal>
            @endforeach
        </div>

        <flux:modal name="New_Exam" class="md:w-96" x-on:close="$wire.resetNewExamForm()">
            <form wire:submit.prevent="save" class="space-y-4">
                <flux:heading size="lg">افزودن آزمون جدید</flux:heading>

                <flux:select wire:model="course_id" label="دوره">
                    <flux:select.option value="">انتخاب دوره</flux:select.option>
                    @foreach($courses as $course)
                        <flux:select.option value="{{ $course->id }}">{{ $course->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="name" label="نام آزمون" />
                <flux:input wire:model="q_number" label="تعداد سوالات" type="number" />
                <flux:input wire:model="start_time" label="تاریخ شروع" type="datetime-local" />
                <flux:input wire:model="end_time" label="تاریخ پایان" type="datetime-local" />

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary">ذخیره</flux:button>
                </div>
            </form>
        </flux:modal>
    </flux:main>
</div>
