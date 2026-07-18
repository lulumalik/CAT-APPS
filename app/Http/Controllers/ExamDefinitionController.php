<?php

namespace App\Http\Controllers;

use App\Models\ExamDefinition;
use App\Models\ExamSubmission;
use App\Models\User;
use App\Services\AutoStudentReportService;
use Illuminate\Http\JsonResponse;
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
            'category' => 'nullable|string',
            'exam_track_id' => 'required|exists:exam_tracks,id',
            'duration' => 'required|integer|min:1',
            'schedule_at' => 'required|date',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'nullable|boolean',
            'question_ids' => 'nullable|array',
        ]);

        $track = \App\Models\ExamTrack::find($data['exam_track_id']);
        $data['category'] = $track?->name ?: ($data['category'] ?? 'Umum');
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
            'category' => 'nullable|string',
            'exam_track_id' => 'sometimes|required|exists:exam_tracks,id',
            'duration' => 'sometimes|required|integer|min:1',
            'schedule_at' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date',
            'is_active' => 'nullable|boolean',
            'question_ids' => 'nullable|array',
        ]);

        if (! empty($data['exam_track_id'])) {
            $track = \App\Models\ExamTrack::find($data['exam_track_id']);
            if ($track) {
                $data['category'] = $track->name;
            }
        }

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
            'exam_track_id',
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

        if ($blocked = $this->registrationRequiredResponse($user)) {
            return $blocked;
        }

        $exams = ExamDefinition::query()
            ->where('is_active', true)
            ->when(
                $user && $user->role === 'user' && $user->exam_track_id,
                fn ($q) => $q->where('exam_track_id', $user->exam_track_id)
            )
            ->orderBy('start_time')
            ->get();

        return response()->json($exams->map(function (ExamDefinition $exam) use ($user) {
            $data = $exam->toArray();
            $attemptsQuery = $user
                ? ExamSubmission::where('user_id', $user->id)->where('exam_definition_id', $exam->id)
                : null;
            $attemptsCount = $attemptsQuery ? (clone $attemptsQuery)->count() : 0;
            $latest = $attemptsCount > 0
                ? (clone $attemptsQuery)->orderByDesc('attempt_number')->orderByDesc('id')->first()
                : null;

            $data['has_submitted'] = $attemptsCount > 0;
            $data['attempts_count'] = $attemptsCount;
            $data['latest_score'] = $latest?->score;
            $data['latest_submission_id'] = $latest?->id;
            $data['latest_attempt_number'] = $latest?->attempt_number;
            // Boleh mengulang selama jadwal ujian masih terbuka.
            $data['can_submit'] = $exam->canSubmit();

            return $data;
        })->values());
    }

    public function show(Request $request, ExamDefinition $exam)
    {
        $user = $request->user();

        if ($blocked = $this->registrationRequiredResponse($user)) {
            return $blocked;
        }

        $attemptsCount = 0;
        if ($user) {
            $attemptsCount = ExamSubmission::where('user_id', $user->id)
                ->where('exam_definition_id', $exam->id)
                ->count();
        }

        $shuffleSeed = $user ? ((int) $user->id + ($attemptsCount * 1000)) : null;

        return response()->json($exam->serializeForExam([
            'has_submitted' => $attemptsCount > 0,
            'attempts_count' => $attemptsCount,
            // Jangan blokir retake di runner — hanya cek jadwal.
            'can_submit' => $exam->canSubmit(),
        ], $shuffleSeed));
    }

    public function submit(Request $request, ExamDefinition $exam)
    {
        $user = $request->user();

        if ($blocked = $this->registrationRequiredResponse($user)) {
            return $blocked;
        }

        if (! $exam->canSubmit()) {
            return response()->json(['message' => 'Ujian sudah berakhir atau belum dimulai'], 422);
        }

        $data = $request->validate([
            'answers' => 'present|array',
        ]);

        $attemptNumber = (int) ExamSubmission::where('user_id', $user->id)
            ->where('exam_definition_id', $exam->id)
            ->max('attempt_number') + 1;

        $score = 0;
        $questions = \App\Models\Question::whereIn('id', $exam->question_ids ?? [])->get();
        foreach ($data['answers'] as $questionId => $userAnswer) {
            $question = $questions->firstWhere('id', (int) $questionId);
            if ($question && $question->type === 'multiple_choice' && (string) $question->correct === (string) $userAnswer) {
                $score++;
            }
        }

        $submission = ExamSubmission::create([
            'user_id' => $user->id,
            'exam_definition_id' => $exam->id,
            'attempt_number' => max(1, $attemptNumber),
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
            'attempt_number' => $submission->attempt_number,
            'submission' => $submission,
            'review' => $this->buildReviewPayload($exam, $submission, $questions),
        ]);
    }

    /**
     * Riwayat percobaan ujian milik siswa yang login (+ ringkasan tinjauan).
     */
    public function mySubmissions(Request $request, ExamDefinition $exam)
    {
        $user = $request->user();

        if ($blocked = $this->registrationRequiredResponse($user)) {
            return $blocked;
        }

        $submissions = ExamSubmission::where('user_id', $user->id)
            ->where('exam_definition_id', $exam->id)
            ->orderByDesc('attempt_number')
            ->orderByDesc('id')
            ->get();

        $total = count($exam->question_ids ?? []);

        return response()->json([
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'category' => $exam->category,
                'total_questions' => $total,
                'can_retake' => $exam->canSubmit(),
            ],
            'submissions' => $submissions->map(fn (ExamSubmission $s) => [
                'id' => $s->id,
                'attempt_number' => $s->attempt_number,
                'score' => $s->score,
                'total' => $total,
                'percent' => $total > 0 ? (int) round(($s->score / $total) * 100) : 0,
                'submitted_at' => optional($s->submitted_at)->toIso8601String(),
            ])->values(),
        ]);
    }

    /**
     * Detail tinjauan satu percobaan (jawaban benar/salah + kunci).
     */
    public function mySubmissionReview(Request $request, ExamDefinition $exam, ExamSubmission $submission)
    {
        $user = $request->user();

        if ($blocked = $this->registrationRequiredResponse($user)) {
            return $blocked;
        }

        if ((int) $submission->user_id !== (int) $user->id
            || (int) $submission->exam_definition_id !== (int) $exam->id) {
            return response()->json(['message' => 'Submisi tidak ditemukan'], 404);
        }

        $questions = \App\Models\Question::whereIn('id', $exam->question_ids ?? [])->get();

        return response()->json($this->buildReviewPayload($exam, $submission, $questions));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\Question>  $questions
     * @return array<string, mixed>
     */
    private function buildReviewPayload(ExamDefinition $exam, ExamSubmission $submission, $questions): array
    {
        $answers = $submission->answers ?? [];
        $orderedIds = $exam->question_ids ?? [];
        $items = [];

        foreach ($orderedIds as $index => $questionId) {
            $question = $questions->firstWhere('id', (int) $questionId);
            if (! $question) {
                continue;
            }

            $userAnswer = $answers[$questionId] ?? $answers[(string) $questionId] ?? null;
            $isMcq = $question->type === 'multiple_choice';
            $isCorrect = $isMcq && $userAnswer !== null && (string) $question->correct === (string) $userAnswer;

            $items[] = [
                'number' => $index + 1,
                'question_id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'options' => $question->options,
                'image_url' => $question->image ?? null,
                'user_answer' => $userAnswer,
                'correct_answer' => $isMcq ? $question->correct : null,
                'is_correct' => $isMcq ? $isCorrect : null,
            ];
        }

        $total = count($orderedIds);

        return [
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'category' => $exam->category,
                'can_retake' => $exam->canSubmit(),
            ],
            'submission' => [
                'id' => $submission->id,
                'attempt_number' => $submission->attempt_number,
                'score' => $submission->score,
                'total' => $total,
                'percent' => $total > 0 ? (int) round(($submission->score / $total) * 100) : 0,
                'submitted_at' => optional($submission->submitted_at)->toIso8601String(),
            ],
            'items' => $items,
            'wrong_count' => collect($items)->where('is_correct', false)->count(),
            'correct_count' => collect($items)->where('is_correct', true)->count(),
        ];
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

    private function registrationRequiredResponse(?User $user): ?JsonResponse
    {
        if (! $user || $user->role !== 'user') {
            return null;
        }

        if ($user->hasCompletedOnboarding()) {
            return null;
        }

        return response()->json([
            'message' => 'Selesaikan pendaftaran terlebih dahulu untuk mengakses ujian.',
        ], 403);
    }
}
