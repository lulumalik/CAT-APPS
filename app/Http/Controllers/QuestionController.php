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
        $data = $request->validate([
            'question' => 'required|string',
            'category' => 'required|string',
            'difficulty' => 'required|string',
            'options' => 'required|array|size:4',
            'correct' => 'required|string|in:A,B,C,D',
        ]);
        $data['created_by'] = optional($request->user())->id;
        $item = Question::create($data);
        return response()->json($item, 201);
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'question' => 'required|string',
            'category' => 'required|string',
            'difficulty' => 'required|string',
            'options' => 'required|array|size:4',
            'correct' => 'required|string|in:A,B,C,D',
        ]);
        $question->update($data);
        return response()->json($question);
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return response()->noContent();
    }
}