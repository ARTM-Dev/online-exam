<?php

use Livewire\Volt\Component;
use App\Models\Exam;
use App\Models\ExamUser;

new class extends Component {

    public $exams = [];

    public function mount(): void
    {
        $this->exams = Exam::where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->get();
    }

    public function hasFinished($examId): bool
    {
        return ExamUser::where('user_id', auth()->id())
            ->where('exam_id', $examId)
            ->where('is_finished', true)
            ->exists();
    }

};
?>

<div>
    <flux:main class="max-w-6xl mx-auto p-6 space-y-6">

        <flux:heading size="lg">
            آزمون‌های فعال
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @forelse($exams as $exam)

                <flux:card class="space-y-4">

                    <flux:heading size="md">
                        {{ $exam->name }}
                    </flux:heading>

                    <div class="text-sm space-y-1">
                        <p>شروع: {{ $exam->start_time }}</p>
                        <p>پایان: {{ $exam->end_time }}</p>
                        <p>تعداد سوال: {{ $exam->questions()->count() }}</p>
                    </div>

                    @if($this->hasFinished($exam->id))

                        <flux:badge color="red">
                            قبلاً شرکت کرده‌اید
                        </flux:badge>

                    @else

                        <flux:button
                            color="green"
                            variant="primary"
                            href="{{ route('exam.take', $exam->id) }}"
                            wire:navigate
                        >
                            شرکت در آزمون
                        </flux:button>

                    @endif

                </flux:card>

            @empty

                <flux:callout type="info">
                    در حال حاضر آزمون فعالی وجود ندارد
                </flux:callout>

            @endforelse

        </div>

    </flux:main>
</div>
