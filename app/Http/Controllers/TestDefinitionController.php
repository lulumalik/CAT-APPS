<?php

namespace App\Http\Controllers;

use App\Models\TestDefinition;
use App\Models\TestSubmission;
use Illuminate\Http\Request;

class TestDefinitionController extends Controller
{
    public function index(Request $request)
    {
        $q = TestDefinition::query();
        if ($search = $request->string('search')->toString()) {
            $q->where('name', 'like', "%$search%");
        }
        return response()->json($q->orderByDesc('schedule_at')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'duration' => 'required|integer|min:1',
            'schedule_at' => 'required|date',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'nullable|boolean',
            'question_ids' => 'required|array|min:1',
        ]);

        // Validasi durasi test harus antara 1-4 jam
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
        $item = TestDefinition::create($data);
        return response()->json($item, 201);
    }

    public function update(Request $request, TestDefinition $test)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'duration' => 'required|integer|min:1',
            'schedule_at' => 'required|date',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'nullable|boolean',
            'question_ids' => 'required|array|min:1',
        ]);

        // Validasi durasi test harus antara 1-4 jam
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

        $test->update($data);
        return response()->json($test);
    }

    public function destroy(TestDefinition $test)
    {
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
            if ($question && $question->correct == $userAnswer) {
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
}