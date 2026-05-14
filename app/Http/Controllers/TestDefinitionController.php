<?php

namespace App\Http\Controllers;

use App\Models\TestDefinition;
use App\Models\FreeTryoutSubmission;
use App\Models\TestSubmission;
use Illuminate\Http\Request;

class TestDefinitionController extends Controller
{
    private function enforceSingleFreeTryout(array $data, ?int $exceptTestId = null): void
    {
        if (! ($data['is_free_tryout'] ?? false)) {
            return;
        }

        $query = TestDefinition::query()->where('is_free_tryout', true);
        if ($exceptTestId) {
            $query->where('id', '!=', $exceptTestId);
        }

        $query->update(['is_free_tryout' => false]);
    }

    private function normalizeRequest(Request $request): void
    {
        foreach ([
            'scheduleAt' => 'schedule_at',
            'startTime' => 'start_time',
            'endTime' => 'end_time',
            'isActive' => 'is_active',
            'isFreeTryout' => 'is_free_tryout',
            'questionIds' => 'question_ids',
        ] as $from => $to) {
            if ($request->has($from)) {
                $request->merge([$to => $request->input($from)]);
            }
        }
    }
    private function enforceQuestionDefaults(array &$data): void
    {
        if (empty($data['question_ids'])) {
            $data['question_ids'] = [];
            $data['is_active'] = false;
        }
    }
    public function index(Request $request)
    {
        $q = TestDefinition::query();
        
        $user = $request->user();
        if ($user && $user->role === 'mentor') {
            $q->where('created_by', $user->id);
        }

        if ($search = $request->string('search')->toString()) {
            $q->where('name', 'like', "%$search%");
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
            'is_free_tryout' => 'nullable|boolean',
            'question_ids' => 'nullable|array',
        ]);

        $data['created_by'] = optional($request->user())->id;
        $this->enforceQuestionDefaults($data);
        $this->enforceSingleFreeTryout($data);
        $item = TestDefinition::create($data);
        return response()->json($item, 201);
    }

    public function update(Request $request, TestDefinition $test)
    {
        $user = $request->user();
        if ($user && $user->role === 'mentor' && $test->created_by !== $user->id) {
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
            'is_free_tryout' => 'nullable|boolean',
            'question_ids' => 'nullable|array',
        ]);

        $effectiveStartTime = $data['start_time'] ?? $test->start_time;
        $effectiveEndTime = $data['end_time'] ?? $test->end_time;

        if (array_key_exists('end_time', $data) && ! empty($effectiveStartTime) && ! empty($effectiveEndTime)) {
            $startTs = strtotime((string) $effectiveStartTime);
            $endTs = strtotime((string) $effectiveEndTime);
            if ($startTs !== false && $endTs !== false && $endTs <= $startTs) {
                return response()->json([
                    'message' => 'Waktu selesai harus setelah waktu mulai',
                    'errors' => [
                        'end_time' => ['Waktu selesai harus setelah waktu mulai']
                    ]
                ], 422);
            }
        }

        $this->enforceQuestionDefaults($data);
        $this->enforceSingleFreeTryout($data, $test->id);
        $test->update($data);
        return response()->json($test);
    }

    public function destroy(Request $request, TestDefinition $test)
    {
        $user = $request->user();
        if ($user && $user->role === 'mentor' && $test->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $test->delete();
        return response()->noContent();
    }

    /**
     * Get incoming tests (upcoming and ongoing)
     */
    public function incoming(Request $request)
    {
        $user = $request->user();
        $query = TestDefinition::available()->visibleToUser($user);

        return response()->json($query->get());
    }

    /**
     * Get available tests (can be started)
     */
    public function available(Request $request)
    {
        $user = $request->user();
        $query = TestDefinition::available()->visibleToUser($user);

        return response()->json($query->get());
    }

    public function freeTryoutList()
    {
        $item = TestDefinition::query()
            ->where('is_free_tryout', true)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->first();

        return response()->json($item ? [$item] : []);
    }

    public function freeTryoutShow(TestDefinition $test)
    {
        if (! $test->is_free_tryout || ! $test->is_active) {
            return response()->json(['message' => 'Tryout gratis tidak tersedia.'], 404);
        }

        return response()->json($test);
    }

    public function freeTryoutSubmit(Request $request, TestDefinition $test)
    {
        if (! $test->is_free_tryout || ! $test->is_active) {
            return response()->json(['message' => 'Tryout gratis tidak tersedia.'], 404);
        }

        if (! $test->canSubmit()) {
            return response()->json(['message' => 'Tryout belum dimulai atau sudah berakhir.'], 422);
        }

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:L,P,Laki-laki,Perempuan',
            'city' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'phone' => 'required|string|max:32',
            'answers' => 'present|array',
        ]);

        $questions = $test->questions;
        $score = 0;
        foreach ($data['answers'] as $questionId => $userAnswer) {
            $question = $questions->firstWhere('id', $questionId);
            if ($question && $question->type === 'multiple_choice' && $question->correct == $userAnswer) {
                $score++;
            }
        }

        $submission = FreeTryoutSubmission::create([
            'test_definition_id' => $test->id,
            'full_name' => $data['full_name'],
            'gender' => $data['gender'],
            'city' => $data['city'],
            'birth_date' => $data['birth_date'],
            'phone' => $data['phone'],
            'answers' => $data['answers'],
            'score' => $score,
            'total_questions' => $questions->count(),
            'submitted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Tryout gratis berhasil disubmit.',
            'score' => $score,
            'total' => $questions->count(),
            'submission' => $submission,
        ]);
    }

    /**
     * Get test detail with questions and check if user already submitted
     */
    public function show(Request $request, TestDefinition $test)
    {
        $user = $request->user();

        if (! $test->canBeAccessedBy($user)) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke tes ini.'], 403);
        }

        // Cek apakah user sudah submit test ini
        $hasSubmitted = false;
        if ($user) {
            $hasSubmitted = TestSubmission::where('user_id', $user->id)
                ->where('test_definition_id', $test->id)
                ->exists();
        }

        $test->has_submitted = $hasSubmitted;

        return response()->json($test);
    }

    /**
     * Submit test answers
     */
    public function submit(Request $request, TestDefinition $test)
    {
        $user = $request->user();

        if (! $test->canBeAccessedBy($user)) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke tes ini.'], 403);
        }

        // Cek apakah user sudah submit test ini
        $existingSubmission = TestSubmission::where('user_id', $user->id)
            ->where('test_definition_id', $test->id)
            ->first();

        if ($existingSubmission) {
            return response()->json([
                'message' => 'Anda sudah mengerjakan test ini sebelumnya'
            ], 422);
        }

        // Validasi test masih dalam waktu pengerjaan
        if (!$test->canSubmit()) {
            return response()->json([
                'message' => 'Test sudah berakhir atau belum dimulai'
            ], 422);
        }

        $data = $request->validate([
            'answers' => 'present|array'
        ]);

        // Hitung score
        $score = 0;
        $questions = $test->questions;
        foreach ($data['answers'] as $questionId => $userAnswer) {
            $question = $questions->firstWhere('id', $questionId);
            if ($question && $question->type === 'multiple_choice' && $question->correct == $userAnswer) {
                $score++;
            }
        }

        $submission = TestSubmission::create([
            'user_id' => $user->id,
            'test_definition_id' => $test->id,
            'answers' => $data['answers'],
            'score' => $score,
            'submitted_at' => now()
        ]);

        return response()->json([
            'message' => 'Test berhasil disubmit',
            'score' => $score,
            'total' => $questions->count(),
            'submission' => $submission
        ]);
    }

    public function myTests(Request $request)
    {
        $user = $request->user();
        $subs = TestSubmission::with('testDefinition')
            ->where('user_id', $user->id)
            ->orderByDesc('submitted_at')
            ->get();
        
        $items = $subs->map(function ($s) {
            $t = $s->testDefinition;
            if (!$t) return null;
            $total = is_array($t->question_ids) ? count($t->question_ids) : ($t->questions ? $t->questions->count() : 0);
            $pct = $total > 0 ? round(($s->score / $total) * 100, 1) : 0;
            return [
                'test_id' => $t->id,
                'name' => $t->name,
                'category' => $t->category,
                'start_time' => $t->start_time,
                'end_time' => $t->end_time,
                'score' => $s->score,
                'total' => $total,
                'percentage' => $pct,
                'submitted_at' => $s->submitted_at,
            ];
        })->filter();

        $avg = $items->count() ? round($items->avg('percentage'), 1) : 0;
        return response()->json([
            'items' => $items->values(),
            'stats' => [
                'completed' => $items->count(),
                'average' => $avg,
            ],
        ]);
    }

    public function testSubmissions(Request $request, TestDefinition $test)
    {
        $user = $request->user();
        if ($user->role === 'mentor' && $test->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $submissions = $test->submissions()->with('user')->orderByDesc('submitted_at')->get();
        return response()->json($submissions);
    }

    public function updateSubmission(Request $request, $id)
    {
        $submission = TestSubmission::findOrFail($id);
        $test = $submission->testDefinition;
        $user = $request->user();
        
        if ($user->role === 'mentor' && $test->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'score' => 'required|integer|min:0',
        ]);

        $submission->update($data);
        return response()->json($submission);
    }

    public function freeTryoutSubmissions(Request $request, TestDefinition $test)
    {
        if (! $test->is_free_tryout) {
            return response()->json(['message' => 'Test ini bukan tryout gratis.'], 422);
        }

        $user = $request->user();
        if ($user->role === 'mentor' && $test->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $items = $test->freeTryoutSubmissions()
            ->orderByDesc('submitted_at')
            ->get();

        return response()->json($items);
    }
}
