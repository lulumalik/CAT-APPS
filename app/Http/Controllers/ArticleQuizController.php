<?php

namespace App\Http\Controllers;

use App\Models\ArticleQuiz;
use App\Models\Question;
use Illuminate\Http\Request;

class ArticleQuizController extends Controller
{
    public function index(Request $request)
    {
        $articles = ArticleQuiz::with(['questions:id,question,category,difficulty,type,batch,article_quiz_id'])
            ->orderByDesc('id')
            ->get();

        return response()->json($articles);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'question_ids' => 'nullable|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $data['created_by'] = optional($request->user())->id;

        $article = ArticleQuiz::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'created_by' => $data['created_by'],
        ]);

        if (!empty($data['question_ids'])) {
            Question::whereIn('id', $data['question_ids'])->update(['article_quiz_id' => $article->id]);
        }

        return response()->json($article->load('questions:id,question,category,difficulty,type,batch,article_quiz_id'), 201);
    }

    public function show(ArticleQuiz $articleQuiz)
    {
        return response()->json($articleQuiz->load('questions:id,question,category,difficulty,type,batch,article_quiz_id'));
    }

    public function update(Request $request, ArticleQuiz $articleQuiz)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'question_ids' => 'nullable|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $articleQuiz->update([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        if (array_key_exists('question_ids', $data)) {
            // Reset existing assigned questions for this article
            Question::where('article_quiz_id', $articleQuiz->id)->update(['article_quiz_id' => null]);
            if (!empty($data['question_ids'])) {
                Question::whereIn('id', $data['question_ids'])->update(['article_quiz_id' => $articleQuiz->id]);
            }
        }

        return response()->json($articleQuiz->load('questions:id,question,category,difficulty,type,batch,article_quiz_id'));
    }

    public function destroy(ArticleQuiz $articleQuiz)
    {
        // Unassign questions
        Question::where('article_quiz_id', $articleQuiz->id)->update(['article_quiz_id' => null]);
        $articleQuiz->delete();

        return response()->noContent();
    }

    public function assignQuestions(Request $request, ArticleQuiz $articleQuiz)
    {
        $data = $request->validate([
            'question_ids' => 'present|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        // Unlink previous questions
        Question::where('article_quiz_id', $articleQuiz->id)->update(['article_quiz_id' => null]);

        // Link new questions
        if (!empty($data['question_ids'])) {
            Question::whereIn('id', $data['question_ids'])->update(['article_quiz_id' => $articleQuiz->id]);
        }

        return response()->json($articleQuiz->load('questions:id,question,category,difficulty,type,batch,article_quiz_id'));
    }
}
