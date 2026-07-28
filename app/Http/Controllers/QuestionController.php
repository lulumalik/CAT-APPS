<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\ArticleQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = Question::with('articleQuiz');

        if ($search = $request->string('search')->toString()) {
            $q->where('question', 'like', "%$search%");
        }
        if ($cat = $request->string('category')->toString()) {
            $q->where('category', $cat);
        }
        if ($dif = $request->string('difficulty')->toString()) {
            $q->where('difficulty', $dif);
        }
        if ($batch = $request->string('batch')->toString()) {
            $q->where('batch', $batch);
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
            'batches' => Question::whereNotNull('batch')->distinct()->pluck('batch')->filter()->values(),
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
            'question' => 'nullable|string',
            'category' => 'required|string',
            'difficulty' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'image' => 'nullable|image|max:2048',
            'article_quiz_id' => 'nullable|exists:article_quizzes,id',
            'batch' => 'nullable|string',
        ];

        if ($request->input('type') === 'multiple_choice') {
            $rules['options'] = 'required|array|min:2|max:6';
            $rules['correct'] = 'required|string|in:A,B,C,D,E,F';
        }

        $data = $request->validate($rules);
        $data['question'] = $data['question'] ?? '';
        $data['batch'] = $data['batch'] ?: 'Tryout 1';

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

        return response()->json($item->load('articleQuiz'), 201);
    }

    public function update(Request $request, Question $question)
    {
        $this->authorizeOwnedByMentor($request, $question);

        $rules = [
            'question' => 'nullable|string',
            'category' => 'required|string',
            'difficulty' => 'required|string',
            'type' => 'required|in:multiple_choice,essay',
            'image' => 'nullable|image|max:2048',
            'article_quiz_id' => 'nullable|exists:article_quizzes,id',
            'batch' => 'nullable|string',
        ];

        if ($request->input('type') === 'multiple_choice') {
            $rules['options'] = 'required|array|min:2|max:6';
            $rules['correct'] = 'required|string|in:A,B,C,D,E,F';
        }

        $data = $request->validate($rules);
        $data['question'] = $data['question'] ?? '';
        $data['batch'] = $data['batch'] ?: 'Tryout 1';

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

        return response()->json($question->load('articleQuiz'));
    }

    public function destroy(Request $request, Question $question)
    {
        $this->authorizeOwnedByMentor($request, $question);
        $question->delete();

        return response()->noContent();
    }

    public function destroyAll(Request $request)
    {
        $user = $request->user();
        $query = Question::query();
        $articleQuery = ArticleQuiz::query();

        if ($user && $user->role === 'mentor') {
            $query->where('created_by', $user->id);
            $articleQuery->where('created_by', $user->id);
        }

        $deletedCount = $query->delete();
        $articleDeletedCount = $articleQuery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank soal dan Article Quiz berhasil dihapus.',
            'deleted_count' => $deletedCount,
            'deleted_article_count' => $articleDeletedCount,
        ]);
    }

    /**
     * Download CSV Template for Excel bulk import
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_soal.csv"',
        ];

        $columns = [
            'batch',
            'category',
            'difficulty',
            'type',
            'question',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e',
            'option_f',
            'correct',
            'article_title',
            'article_content'
        ];

        $sampleData = [
            [
                'batch' => 'Tryout 1',
                'category' => 'Sinonim',
                'difficulty' => 'Easy',
                'type' => 'multiple_choice',
                'question' => 'Anomali = ....',
                'option_a' => 'Penyimpangan',
                'option_b' => 'Kesamaan',
                'option_c' => 'Keanehan',
                'option_d' => 'Keteraturan',
                'option_e' => 'Normal',
                'correct' => 'A',
                'article_title' => '',
                'article_content' => '',
            ],
            [
                'batch' => 'Tryout 1',
                'category' => 'Pemahaman Bahasa',
                'difficulty' => 'Medium',
                'type' => 'multiple_choice',
                'question' => 'Ide pokok paragraf di atas adalah ....',
                'option_a' => 'Wortel kaya akan vitamin A',
                'option_b' => 'Wortel adalah tanaman obat',
                'option_c' => 'Vitamin A bagus untuk mata',
                'option_d' => 'Penggunaan pil vitamin A',
                'option_e' => 'Wortel berwarna orange',
                'correct' => 'A',
                'article_title' => 'Bacaan Wortel & Vitamin A',
                'article_content' => 'Bagi anda yang sering mengkonsumsi pil vitamin A, sebaiknya anda beralih pada sayuran berwarna orange seperti wortel...',
            ],
            [
                'batch' => 'Tryout 2',
                'category' => 'Deret Angka',
                'difficulty' => 'Medium',
                'type' => 'multiple_choice',
                'question' => '2, 4, 8, 16, ....',
                'option_a' => '32',
                'option_b' => '24',
                'option_c' => '20',
                'option_d' => '30',
                'option_e' => '64',
                'correct' => 'A',
                'article_title' => '',
                'article_content' => '',
            ],
        ];

        $callback = function () use ($columns, $sampleData) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($sampleData as $row) {
                fputcsv($file, [
                    $row['batch'],
                    $row['category'],
                    $row['difficulty'],
                    $row['type'],
                    $row['question'],
                    $row['option_a'],
                    $row['option_b'],
                    $row['option_c'],
                    $row['option_d'],
                    $row['option_e'],
                    $row['option_f'] ?? '',
                    $row['correct'],
                    $row['article_title'],
                    $row['article_content'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk import questions from CSV / Excel export
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
            'default_batch' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return response()->json(['message' => 'Gagal membaca file.'], 422);
        }

        // Check for BOM and skip it if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fputcsv_array_clean(fgetcsv($handle, 4096, ','));
        if (!$header || count($header) < 5) {
            // Try with semicolon separator if comma fails
            rewind($handle);
            if ($bom === "\xEF\xBB\xBF") fread($handle, 3);
            $header = fputcsv_array_clean(fgetcsv($handle, 4096, ';'));
            $delimiter = ';';
        } else {
            $delimiter = ',';
        }

        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'Format file tidak valid / header tidak ditemukan.'], 422);
        }

        $headerMap = [];
        foreach ($header as $idx => $col) {
            $headerMap[strtolower(trim($col))] = $idx;
        }

        $user = $request->user();
        $importedCount = 0;
        $articleMap = [];
        $defaultBatch = $request->input('default_batch') ?: 'Tryout 1';

        while (($row = fgetcsv($handle, 4096, $delimiter)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $getVal = function ($key) use ($row, $headerMap) {
                if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                    return trim($row[$headerMap[$key]]);
                }
                return '';
            };

            $questionText = $getVal('question');
            if (!$questionText) continue;

            $batch = $getVal('batch') ?: $defaultBatch;
            $category = $getVal('category') ?: 'Umum';
            $difficulty = $getVal('difficulty') ?: 'Medium';
            $type = strtolower($getVal('type')) ?: 'multiple_choice';
            $correct = strtoupper($getVal('correct')) ?: 'A';

            $artTitle = $getVal('article_title');
            $artContent = $getVal('article_content');
            $articleId = null;

            if (!empty($artTitle)) {
                if (!isset($articleMap[$artTitle])) {
                    $artModel = ArticleQuiz::where('title', $artTitle)->first();
                    if (!$artModel && !empty($artContent)) {
                        $artModel = ArticleQuiz::create([
                            'title' => $artTitle,
                            'content' => $artContent,
                            'created_by' => optional($user)->id,
                        ]);
                    }
                    if ($artModel) {
                        $articleMap[$artTitle] = $artModel->id;
                    }
                }
                $articleId = $articleMap[$artTitle] ?? null;
            }

            $options = [];
            foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $optKey) {
                $val = $getVal('option_' . $optKey);
                if ($val !== '') {
                    $options[] = [
                        'key' => strtoupper($optKey),
                        'label' => $val,
                    ];
                }
            }

            if (empty($options) && $type === 'multiple_choice') {
                $options = [
                    ['key' => 'A', 'label' => 'Opsi A'],
                    ['key' => 'B', 'label' => 'Opsi B'],
                    ['key' => 'C', 'label' => 'Opsi C'],
                    ['key' => 'D', 'label' => 'Opsi D'],
                    ['key' => 'E', 'label' => 'Opsi E'],
                ];
            }

            Question::create([
                'batch' => $batch,
                'question' => $questionText,
                'category' => $category,
                'difficulty' => ucfirst(strtolower($difficulty)),
                'type' => $type,
                'options' => $options,
                'correct' => $correct,
                'article_quiz_id' => $articleId,
                'created_by' => optional($user)->id,
            ]);

            $importedCount++;
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$importedCount} soal!",
            'imported_count' => $importedCount,
        ]);
    }

    private function authorizeOwnedByMentor(Request $request, Question $question): void
    {
        $user = $request->user();
        if ($user && $user->role === 'mentor' && (int) $question->created_by !== (int) $user->id) {
            abort(403, 'Unauthorized');
        }
    }
}

function fputcsv_array_clean($row)
{
    if (!$row) return [];
    return array_map(function ($val) {
        return preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', trim($val));
    }, $row);
}
