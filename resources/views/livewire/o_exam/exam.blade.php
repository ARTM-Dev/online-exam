<?php

use App\Models\Course;
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

    public function mount(): void
    {
        $this->exams = Exam::with('course')->get();
        $this->get_course();
    }

    public function get_exam(): void
    {
        $this->exams = Exam::all();
    }

    public function get_course(): void
    {
        $this->courses = Course::all();
    }

    public function save()
    {

        $this->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string',
            'q_number' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        Exam::create([
            'name' => $this->name,
            'course_id' => $this->course_id,
            'q_number' => $this->q_number,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        Flux::modal('New_Exam')->close();
        $this->get_exam();

    }

    public $delete_id = '';

    public function deleting_id($id): void
    {
        $this->delete_id = $id;
    }

    public function delete_exam(): void
    {

        Exam::find($this->delete_id)->delete();

        Flux::modal('delete_exam' . $this->delete_id)->close();
        $this->get_exam();

        $this->delete_id = '';
    }

    public $edit_id = '';

    public $edit_course_id = '';
    public $edit_name = '';
    public $edit_q_number = '';
    public $edit_start_time = '';
    public $edit_end_time = '';

    public function edit_exam($id): void
    {


        $this->edit_id = $id;

        $exam = Exam::find($this->edit_id);

        $this->edit_course_id = $exam->course_id;
        $this->edit_name = $exam->name;
        $this->edit_q_number = $exam->q_number;
        $this->edit_start_time = $exam->start_time;
        $this->edit_end_time = $exam->end_time;
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

        Flux::modal('edit_exam' . $this->edit_id)->close();

        $this->get_exam();

        $this->reset([
            'edit_id',
            'edit_course_id',
            'edit_name',
            'edit_q_number',
            'edit_start_time',
            'edit_end_time',
        ]);
    }





}; ?>


<div>

    <flux:main class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($exams as $exam)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 hover:shadow-xl transition">
                <h2 class="text-lg font-bold mb-2 text-purple-600">آزمون #{{ $exam->id }}</h2>
                <p><span class="font-semibold">نام:</span> {{ $exam->name }}</p>
                <p><span class="font-semibold">دوره:</span> {{ $exam->course->name ?? 'نامشخص' }}</p>
                <p><span class="font-semibold">تعداد سوالات:</span> {{ $exam->q_number }}</p>
                <p><span class="font-semibold">شروع:</span> {{ $exam->start_time }}</p>
                <p><span class="font-semibold">پایان:</span> {{ $exam->end_time }}</p>
                <p><span class="font-semibold">وضعیت:</span>
                    @if($exam->status === 'upcoming')
                        <span class="text-blue-500 font-bold">بزودی</span>
                    @elseif($exam->status === 'active')
                        <span class="text-green-500 font-bold">فعال</span>
                    @elseif($exam->status === 'closed')
                        <span class="text-red-500 font-bold">تمام شده</span>
                    @endif
                </p>

                <br>

                <flux:modal.trigger :name="'delete_exam'.$exam->id">
                    <flux:button wire:click="deleting_id({{$exam->id}})" >{{__('حذف آزمون')}}</flux:button>
                </flux:modal.trigger>

                <flux:modal :name="'delete_exam'.$exam->id" class="md:w-96">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{__('حذف ')}} {{$exam->name}}</flux:heading>
                        </div>

                        <form wire:submit="delete_exam()" class="space-y-6" autocomplete="off">


                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary" color="blue">{{__('حذف')}}</flux:button>
                            </div>
                        </form>

                    </div>
                </flux:modal>

                <flux:modal.trigger :name="'edit_exam'.$exam->id">
                    <flux:button
                        wire:click="edit_exam({{$exam->id}})"
                        size="sm"
                        variant="primary"
                        color="gray"
                        class="cursor-pointer mt-2"
                    >
                        {{__('ویرایش آزمون')}}
                    </flux:button>
                </flux:modal.trigger>

                <flux:modal :name="'edit_exam'.$exam->id" class="md:w-96" :dismissible="false">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{__('ویرایش آزمون')}}</flux:heading>
                            <flux:text class="mt-2">{{__('اطلاعات را ویرایش کنید')}}</flux:text>
                        </div>

                        <form wire:submit="update_exam()" class="space-y-6" autocomplete="off">

                            <label class="block text-sm font-medium mb-1">نام دوره</label>
                            <flux:select wire:model="edit_course_id" size="sm">
                                @foreach($courses as $course)
                                    <flux:select.option value="{{ $course->id }}">
                                        {{ $course->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:input wire:model="edit_name" label="نام" placeholder="نام آزمون" class:input="text-center"/>
                            <flux:input wire:model="edit_q_number" label="تعداد سوالات" placeholder="تعداد سوالات" type="number"/>
                            <flux:input wire:model="edit_start_time" label="تاریخ شروع" type="datetime-local" />
                            <flux:input wire:model="edit_end_time" label="تاریخ پایان" type="datetime-local"/>


                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary" color="blue">
                                    {{__('ذخیره تغییرات')}}
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </flux:modal>
            </div>

        @endforeach
            <flux:modal.trigger name="New_Exam">
                <flux:button>افزودن آزمون</flux:button>
            </flux:modal.trigger>

            <flux:modal name="New_Exam" class="md:w-96">
                <form wire:submit.prevent="save">
                    <div class="space-y-6">

                        <div>
                            <flux:heading size="lg">{{ __('افزودن آزمون') }}</flux:heading>
                            <flux:text class="mt-2">{{ __('اطلاعات خواسته شده را وارد کنید') }}</flux:text>
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نام دوره</label>
                            <flux:select name="course_id" wire:model="course_id" size="sm" placeholder="انتخاب دوره">
                                @foreach($courses as $course)
                                    <flux:select.option value="{{ $course->id }}">
                                        {{ $course->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <flux:input label="نام آزمون" wire:model="name" placeholder="نام آزمون"/>

                        <flux:input label="تعداد سوالات" wire:model="q_number" type="number" placeholder="تعداد سوالات" />

                        <flux:input label="تاریخ شروع" wire:model="start_time" type="datetime-local" />

                        <flux:input label="تاریخ پایان" wire:model="end_time" type="datetime-local" />

                        <div class="flex justify-end">
                            <flux:button type="submit" variant="primary">ذخیره</flux:button>
                        </div>

                    </div>
                </form>
            </flux:modal>

    </flux:main>


</div>
