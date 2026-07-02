<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = Question::query();

        if ($search = $request->string('search')->toString()) {
            $q->where('question', 'like', "%$search%");
        }
        if ($cat = $request->string('category')->toString()) {
            $q->where('category', $cat);
        }
        if ($dif = $request->string('difficulty')->toString()) {
            $q->where('difficulty', $dif);
        }

        if ($user && $user->role === 'mentor') {
            $q->orderByRaw('CASE WHEN created_by = ? THEN 0 ELSE 1 END', [$user->id])
                ->orderByDesc('id');
        } else {
            $q->orderByDesc('id');
        }

        $statsQuery = Question::query();

        return response()->json([
            'items' => $q->get(),
            'stats' => [
                'total' => (clone $statsQuery)->count(),
                'easy' => (clone $statsQuery)->where('difficulty', 'Easy')->count(),
                'medium' => (clone $statsQuery)->where('difficulty', 'Medium')->count(),
                'hard' => (clone $statsQuery)->where('difficulty', 'Hard')->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'question' => 'required|string',
            'category' => 'required|string',
            'difficulty' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'image' => 'nullable|image|max:2048',
        ];

        if ($request->input('type') === 'multiple_choice') {
            $rules['options'] = 'required|array|size:4';
            $rules['correct'] = 'required|string|in:A,B,C,D';
        }

        $data = $request->validate($rules);

        if ($request->hasFile('image')) {
            $disk = config('filesystems.upload_disk', 'public');
            $path = $request->file('image')->store('questions', $disk);
            $data['image'] = $disk === 'public'
                ? '/storage/'.$path
                : Storage::disk($disk)->url($path);
        }

        if ($request->input('type') === 'essay') {
            $data['options'] = [];
            $data['correct'] = '';
        }

        $data['created_by'] = optional($request->user())->id;
        $item = Question::create($data);

        return response()->json($item, 201);
    }

    public function update(Request $request, Question $question)
    {
        $this->authorizeOwnedByMentor($request, $question);

        $rules = [
            'question' => 'required|string',
            'category' => 'required|string',
            'difficulty' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'image' => 'nullable|image|max:2048',
        ];

        if ($request->input('type') === 'multiple_choice') {
            $rules['options'] = 'required|array|size:4';
            $rules['correct'] = 'required|string|in:A,B,C,D';
        }

        $data = $request->validate($rules);

        if ($request->hasFile('image')) {
            $disk = config('filesystems.upload_disk', 'public');
            $path = $request->file('image')->store('questions', $disk);
            $data['image'] = $disk === 'public'
                ? '/storage/'.$path
                : Storage::disk($disk)->url($path);
        }

        if ($request->input('type') === 'essay') {
            $data['options'] = [];
            $data['correct'] = '';
        }

        $question->update($data);

        return response()->json($question);
    }

    public function destroy(Request $request, Question $question)
    {
        $this->authorizeOwnedByMentor($request, $question);
        $question->delete();

        return response()->noContent();
    }

    private function authorizeOwnedByMentor(Request $request, Question $question): void
    {
        $user = $request->user();
        if ($user && $user->role === 'mentor' && (int) $question->created_by !== (int) $user->id) {
            abort(403, 'Unauthorized');
        }
    }
}
