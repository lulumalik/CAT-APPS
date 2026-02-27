<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $q = Question::query();
        if ($search = $request->string('search')->toString()) {
            $q->where('question', 'like', "%$search%");
        }
        if ($cat = $request->string('category')->toString()) { $q->where('category', $cat); }
        if ($dif = $request->string('difficulty')->toString()) { $q->where('difficulty', $dif); }
        return response()->json([
            'items' => $q->orderByDesc('id')->get(),
            'stats' => [
                'total' => Question::count(),
                'easy' => Question::where('difficulty','Easy')->count(),
                'medium' => Question::where('difficulty','Medium')->count(),
                'hard' => Question::where('difficulty','Hard')->count(),
            ]
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
             $path = $request->file('image')->store('questions', 'public');
             $data['image'] = '/storage/' . $path;
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
             $path = $request->file('image')->store('questions', 'public');
             $data['image'] = '/storage/' . $path;
        }

        if ($request->input('type') === 'essay') {
            $data['options'] = [];
            $data['correct'] = '';
        }

        $question->update($data);
        return response()->json($question);
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return response()->noContent();
    }
}