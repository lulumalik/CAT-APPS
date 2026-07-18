<?php

namespace App\Http\Controllers;

use App\Models\ExamCategory;
use App\Models\ExamTrack;
use Illuminate\Http\Request;

class ExamCategoryController extends Controller
{
    /**
     * Katalog publik: kategori aktif beserta track aktifnya (untuk signup / pilih minat).
     */
    public function publicIndex()
    {
        $categories = ExamCategory::query()
            ->where('is_active', true)
            ->with(['tracks' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Daftar lengkap untuk admin (termasuk nonaktif + jumlah soal per track).
     */
    public function adminIndex()
    {
        $categories = ExamCategory::query()
            ->with(['tracks' => fn ($q) => $q->withCount(['questions', 'users'])])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category = ExamCategory::create($data + ['is_active' => $data['is_active'] ?? true]);

        return response()->json($category->load('tracks'), 201);
    }

    public function updateCategory(Request $request, ExamCategory $category)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->update($data);

        return response()->json($category->load('tracks'));
    }

    public function destroyCategory(ExamCategory $category)
    {
        $hasUsers = $category->tracks()->withCount('users')->get()->sum('users_count') > 0;
        if ($hasUsers) {
            return response()->json([
                'message' => 'Kategori tidak dapat dihapus karena masih dipilih oleh peserta. Nonaktifkan saja.',
            ], 422);
        }

        $category->delete();

        return response()->noContent();
    }

    public function storeTrack(Request $request)
    {
        $data = $request->validate([
            'exam_category_id' => 'required|exists:exam_categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $track = ExamTrack::create($data + ['is_active' => $data['is_active'] ?? true]);

        return response()->json($track->load('category'), 201);
    }

    public function updateTrack(Request $request, ExamTrack $track)
    {
        $data = $request->validate([
            'exam_category_id' => 'sometimes|required|exists:exam_categories,id',
            'name' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $track->update($data);

        return response()->json($track->load('category'));
    }

    public function destroyTrack(ExamTrack $track)
    {
        if ($track->users()->exists()) {
            return response()->json([
                'message' => 'Track tidak dapat dihapus karena masih dipilih oleh peserta. Nonaktifkan saja.',
            ], 422);
        }

        $track->delete();

        return response()->noContent();
    }
}
