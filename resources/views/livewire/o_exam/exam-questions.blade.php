<?php

use Livewire\Volt\Component;
use App\Models\Exam;

new class extends Component {

    public Exam $exam;

    public function mount(Exam $exam): void
    {
        if (auth()->user()->role !== 'admin'){
            abort(403);
        }
        $this->exam = $exam->load(['questions.course']);
    }

};
?>
<div>
    <flux:main class="p-6 space-y-6">

        <flux:heading size="lg">
            سوالات آزمون: {{ $exam->name }}
        </flux:heading>

        @foreach($exam->questions as $question)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-2">

                <p class="font-semibold">
                    {{ $question->question_text }}
                </p>

                <ul class="text-sm space-y-1">
                    <li>A) {{ $question->option_1 }}</li>
                    <li>B) {{ $question->option_2 }}</li>
                    <li>C) {{ $question->option_3 }}</li>
                    <li>D) {{ $question->option_4 }}</li>
                </ul>

                <p class="text-green-600 font-bold">
                    پاسخ صحیح:
                    {{ ['A','B','C','D'][$question->answer - 1] }}
                </p>

            </div>
        @endforeach

    </flux:main>
</div>
