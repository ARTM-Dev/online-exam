<?php

use Livewire\Volt\Component;
use App\Models\Exam;
use App\Models\ExamUser;
use App\Models\ExamUserAnswer;
use App\Models\ExamResult;
use Illuminate\Support\Facades\Auth;

new class extends Component {

    public Exam $exam;

    public array $answers = [];

    public int $currentIndex = 0;
    public int $score = 0;

    public bool $submitted = false;
    public bool $alreadyFinished = false;

    public int $remainingSeconds = 0;
    public bool $timeIsUp = false;

    public function mount(Exam $exam): void
    {

        if (auth()->user()->role === 'admin') {
            abort(403);
        }

        if ($exam->status !== 'active') {
            abort(403);
        }
        if (now()->lt($exam->start_time) || now()->gt($exam->end_time)) {
            abort(403);
        }

        $this->exam = $exam->load('questions');

        $isFinished = ExamUser::where([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
        ])->value('is_finished');

        if ($isFinished) {
            $this->alreadyFinished = true;
            return;
        }

        ExamUser::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'exam_id' => $exam->id,
            ],
            [
                'is_finished' => false,
            ]
        );

        $this->currentIndex = ExamUserAnswer::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->count();

        if ($this->currentIndex >= $this->exam->questions->count()) {
            $this->finishExam();
        }

        $this->remainingSeconds = now()->diffInSeconds(
            $this->exam->end_time,
            false
        );

        if ($this->remainingSeconds <= 0) {
            $this->timeIsUp = true;
            $this->finishExam();
        }
    }

    public function nextQuestion(): void
    {
        if ($this->timeIsUp || $this->submitted || $this->alreadyFinished) {
            return;
        }

        $question = $this->exam->questions[$this->currentIndex];
        $selected = $this->answers[$question->id] ?? null;

        if (! $selected) {
            return;
        }

        $exists = ExamUserAnswer::where([
            'user_id' => Auth::id(),
            'exam_id' => $this->exam->id,
            'question_id' => $question->id,
        ])->exists();

        if (! $exists) {
            ExamUserAnswer::create([
                'user_id' => Auth::id(),
                'exam_id' => $this->exam->id,
                'question_id' => $question->id,
                'selected_answer' => $selected,
                'is_correct' => ((int) $selected === (int) $question->answer),
            ]);
        }

        if ($this->currentIndex < $this->exam->questions->count() - 1) {
            $this->currentIndex++;
        } else {
            $this->finishExam();
        }
    }

    public function tick(): void
    {
        if ($this->submitted || $this->alreadyFinished) {
            return;
        }

        $this->remainingSeconds = now()->diffInSeconds(
            $this->exam->end_time,
            false
        );

        if ($this->remainingSeconds <= 0) {
            $this->timeIsUp = true;
            $this->finishExam();
        }
    }

    public function finishExam(): void
    {
        if ($this->submitted || $this->alreadyFinished) {
            return;
        }

        $this->score = ExamUserAnswer::where('user_id', Auth::id())
            ->where('exam_id', $this->exam->id)
            ->where('is_correct', true)
            ->count();

        ExamResult::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'exam_id' => $this->exam->id,
            ],
            [
                'score' => $this->score,
            ]
        );

        ExamUser::where([
            'user_id' => Auth::id(),
            'exam_id' => $this->exam->id,
        ])->update([
            'is_finished' => true,
        ]);

        $this->submitted = true;
    }
};
?>

<div>
    <flux:main class="max-w-3xl mx-auto p-6 space-y-6">

        <flux:card>
            <flux:heading size="lg">
                آزمون: {{ $exam->name }}
            </flux:heading>
        </flux:card>

        @if($alreadyFinished)
            <flux:callout type="danger" icon="x-circle">
                شما قبلاً در این آزمون شرکت کرده‌اید
            </flux:callout>

        @elseif($submitted)

            <flux:callout type="success" icon="check-circle">
                <p class="font-bold">آزمون ثبت شد ✅</p>
                <p class="mt-1">
                    نمره شما:
                    {{ $score }} / {{ $exam->questions->count() }}
                </p>
            </flux:callout>

            @if($timeIsUp)
                <flux:callout type="warning" icon="clock">
                    زمان آزمون به پایان رسید ⏳
                </flux:callout>
            @endif

            @php
                $userAnswers = \App\Models\ExamUserAnswer::where('user_id', auth()->id())
                    ->where('exam_id', $exam->id)
                    ->get()
                    ->keyBy('question_id');
            @endphp

            <flux:card class="space-y-6">
                <flux:heading size="md">
                    مرور پاسخ‌ها
                </flux:heading>

                @foreach($exam->questions as $index => $question)
                    @php
                        $answer = $userAnswers[$question->id] ?? null;
                    @endphp

                    <div class="border rounded-lg p-4 space-y-3">
                        <p class="font-bold">
                            سوال {{ $index + 1 }}:
                            {{ $question->question_text }}
                        </p>

                        <div class="space-y-2 text-sm">
                            @foreach([1,2,3,4] as $opt)
                                <div
                                    class="p-2 border rounded
                            @if($question->answer == $opt)
                                bg-green-100 border-green-400
                            @elseif($answer && $answer->selected_answer == $opt && ! $answer->is_correct)
                                bg-red-100 border-red-400
                            @endif
                        "
                                >
                        <span class="font-bold">
                            {{ ['A','B','C','D'][$opt-1] }}.
                        </span>

                                    {{ $question->{'option_'.$opt} }}

                                    @if($question->answer == $opt)
                                        <span class="text-green-600 text-xs mr-2">
                                ✅ پاسخ صحیح
                            </span>
                                    @endif

                                    @if($answer && $answer->selected_answer == $opt)
                                        <span class="text-blue-600 text-xs">
                                🧑‍🎓 پاسخ شما
                            </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </flux:card>


        @else

            <flux:card class="mb-4">
                <span class="font-bold text-red-600">
                    ⏳ زمان باقی‌مانده:
                    {{ gmdate('H:i:s', max(0, $remainingSeconds)) }}
                </span>
            </flux:card>

            @php
                $question = $exam->questions[$currentIndex];
            @endphp

            <flux:card class="space-y-6">
                <flux:badge color="purple">
                    سوال {{ $currentIndex + 1 }}
                    از {{ $exam->questions->count() }}
                </flux:badge>

                <flux:heading size="md">
                    {{ $question->question_text }}
                </flux:heading>

                <div class="grid gap-3">
                    @foreach([1,2,3,4] as $opt)
                        <label class="flex gap-3 p-3 border rounded-lg cursor-pointer">
                            <input
                                type="radio"
                                wire:model="answers.{{ $question->id }}"
                                value="{{ $opt }}"
                            >
                            <span class="font-bold">
                                {{ ['A','B','C','D'][$opt-1] }}
                            </span>
                            <span>
                                {{ $question->{'option_'.$opt} }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end">
                    <flux:button wire:click="nextQuestion">
                        {{ $currentIndex + 1 === $exam->questions->count()
                            ? 'پایان آزمون'
                            : 'سوال بعدی' }}
                    </flux:button>
                </div>
            </flux:card>

        @endif


        <div wire:poll.1s="tick"></div>

        <script>
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    @this.call('finishExam');
                }
            });

            window.addEventListener('blur', () => {
                @this.call('finishExam');
            });
        </script>

    </flux:main>
</div>
