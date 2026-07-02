<?php

namespace App\Http\Controllers;

use App\Models\ExamDefinition;
use App\Models\ExamSubmission;
use App\Services\AutoStudentReportService;
use Illuminate\Http\Request;

class ExamDefinitionController extends Controller
{
    private function normalizeRequest(Request $request): void
    {
        foreach ([
            'scheduleAt' => 'schedule_at',
            'startTime' => 'start_time',
            'endTime' => 'end_time',
            'isActive' => 'is_active',
            'questionIds' => 'question_ids',
        ] as $from => $to) {
            if ($request->has($from)) {
                $request->merge([$to => $request->input($from)]);
            }
        }
    }

    private function enforceQuestionDefaults(array &$data, bool $onlyWhenProvided = false): void
    {
        if ($onlyWhenProvided && ! array_key_exists('question_ids', $data)) {
            return;
        }

        if (empty($data['question_ids'])) {
            $data['question_ids'] = [];
            $data['is_active'] = false;
        }
    }

    public function index(Request $request)
    {
        $q = ExamDefinition::query();
        $user = $request->user();
        if ($user && $user->role === 'mentor') {
            $q->where('created_by', $user->id);
        }

        if ($search = $request->string('search')->toString()) {
            $q->where('name', 'like', "%{$search}%");
        }

        return response()->json($q->orderByDesc('schedule_at')->get());
    }

    public function store(Request $request)
    {
        $this->normalizeRequest($request);
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'duration' => 'required|integer|min:1',
            'schedule_at' => 'required|date',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'nullable|boolean',
            'question_ids' => 'nullable|array',
        ]);

        $data['created_by'] = optional($request->user())->id;
        $this->enforceQuestionDefaults($data);
        $item = ExamDefinition::create($data);

        return response()->json($item, 201);
    }

    public function update(Request $request, ExamDefinition $exam)
    {
        $user = $request->user();
        if ($user && $user->role === 'mentor' && $exam->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->normalizeRequest($request);
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|string',
            'duration' => 'sometimes|required|integer|min:1',
            'schedule_at' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date',
            'is_active' => 'nullable|boolean',
            'question_ids' => 'nullable|array',
        ]);

        $effectiveStartTime = $data['start_time'] ?? $exam->start_time;
        $effectiveEndTime = $data['end_time'] ?? $exam->end_time;
        if (array_key_exists('end_time', $data) && ! empty($effectiveStartTime) && ! empty($effectiveEndTime)) {
            $startTs = strtotime((string) $effectiveStartTime);
            $endTs = strtotime((string) $effectiveEndTime);
            if ($startTs !== false && $endTs !== false && $endTs <= $startTs) {
                return response()->json([
                    'message' => 'Waktu selesai harus setelah waktu mulai',
                    'errors' => [
                        'end_time' => ['Waktu selesai harus setelah waktu mulai'],
                    ],
                ], 422);
            }
        }

        $this->enforceQuestionDefaults($data, true);
        $exam->update($data);

        return response()->json($exam);
    }

    public function destroy(Request $request, ExamDefinition $exam)
    {
        $user = $request->user();
        if ($user && $user->role === 'mentor' && $exam->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $exam->delete();

        return response()->noContent();
    }

    public function duplicate(Request $request, ExamDefinition $exam)
    {
        $user = $request->user();
        if ($user && $user->role === 'mentor' && $exam->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $copy = $exam->replicate([
            'name',
            'description',
            'category',
            'duration',
            'schedule_at',
            'start_time',
            'end_time',
            'question_ids',
        ]);
        $copy->name = "{$exam->name} (Duplikat)";
        $copy->is_active = false;
        $copy->created_by = optional($user)->id;
        $copy->save();

        return response()->json($copy, 201);
    }

    public function available(Request $request)
    {
        $user = $request->user();

        $exams = ExamDefinition::query()
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return response()->json($exams->map(function (ExamDefinition $exam) use ($user) {
            $data = $exam->toArray();
            $hasSubmitted = $user
                ? ExamSubmission::where('user_id', $user->id)
                    ->where('exam_definition_id', $exam->id)
                    ->exists()
                : false;

            $data['has_submitted'] = $hasSubmitted;
            $data['can_submit'] = ! $hasSubmitted && $exam->canSubmit();

            return $data;
        })->values());
    }

    public function show(Request $request, ExamDefinition $exam)
    {
        $hasSubmitted = false;
        $user = $request->user();
        if ($user) {
            $hasSubmitted = ExamSubmission::where('user_id', $user->id)
                ->where('exam_definition_id', $exam->id)
                ->exists();
        }

        return response()->json($exam->serializeForExam(['has_submitted' => $hasSubmitted]));
    }

    public function submit(Request $request, ExamDefinition $exam)
    {
        $user = $request->user();

        $existing = ExamSubmission::where('user_id', $user->id)
            ->where('exam_definition_id', $exam->id)
            ->first();
        if ($existing) {
            return response()->json(['message' => 'Anda sudah mengerjakan ujian ini sebelumnya'], 422);
        }

        if (! $exam->canSubmit()) {
            return response()->json(['message' => 'Ujian sudah berakhir atau belum dimulai'], 422);
        }

        $data = $request->validate([
            'answers' => 'present|array',
        ]);

        $score = 0;
        $questions = \App\Models\Question::whereIn('id', $exam->question_ids ?? [])->get();
        foreach ($data['answers'] as $questionId => $userAnswer) {
            $question = $questions->firstWhere('id', $questionId);
            if ($question && $question->type === 'multiple_choice' && $question->correct == $userAnswer) {
                $score++;
            }
        }

        $submission = ExamSubmission::create([
            'user_id' => $user->id,
            'exam_definition_id' => $exam->id,
            'answers' => $data['answers'],
            'score' => $score,
            'submitted_at' => now(),
        ]);

        app(AutoStudentReportService::class)->fromExamSubmission(
            $user,
            $exam,
            $submission,
            $user->id,
        );

        return response()->json([
            'message' => 'Ujian berhasil disubmit',
            'score' => $score,
            'total' => $questions->count(),
            'submission' => $submission,
        ]);
    }

    public function examSubmissions(Request $request, ExamDefinition $exam)
    {
        $user = $request->user();
        if ($user && $user->role === 'mentor' && $exam->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $submissions = $exam->submissions()->with('user:id,name,email,username')->orderByDesc('submitted_at')->get();

        return response()->json([
            'assessment' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'category' => $exam->category,
                'duration' => $exam->duration,
                'question_ids' => $exam->question_ids ?? [],
                'total_questions' => count($exam->question_ids ?? []),
                'type' => 'exam',
            ],
            'submissions' => $submissions,
        ]);
    }
}
