<?php

namespace App\Http\Controllers;

use App\Models\TestDefinition;
use App\Models\TestSubmission;
use Illuminate\Http\Request;

class TestDefinitionController extends Controller
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
            'question_ids' => 'nullable|array',
        ]);

        $startTime = new \DateTime($data['start_time']);
        $endTime = new \DateTime($data['end_time']);
        $durationInHours = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;

        if ($durationInHours < 1 || $durationInHours > 4) {
            return response()->json([
                'message' => 'Durasi test harus antara 1 sampai 4 jam',
                'errors' => [
                    'end_time' => ['Durasi test harus antara 1 sampai 4 jam']
                ]
            ], 422);
        }

        $data['created_by'] = optional($request->user())->id;
        $this->enforceQuestionDefaults($data);
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

        $startTime = new \DateTime($data['start_time']);
        $endTime = new \DateTime($data['end_time']);
        $durationInHours = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;

        if ($durationInHours < 1 || $durationInHours > 4) {
            return response()->json([
                'message' => 'Durasi test harus antara 1 sampai 4 jam',
                'errors' => [
                    'end_time' => ['Durasi test harus antara 1 sampai 4 jam']
                ]
            ], 422);
        }

        $this->enforceQuestionDefaults($data);
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
        $tests = TestDefinition::available()->get();
        return response()->json($tests);
    }

    /**
     * Get available tests (can be started)
     */
    public function available(Request $request)
    {
        $tests = TestDefinition::available()->get();
        return response()->json($tests);
    }

    /**
     * Get test detail with questions and check if user already submitted
     */
    public function show(Request $request, TestDefinition $test)
    {
        // Cek apakah user sudah submit test ini
        $hasSubmitted = false;
        if ($user = $request->user()) {
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
            'answers' => 'required|array'
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
}
