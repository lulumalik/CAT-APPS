<?php

namespace App\Http\Controllers;

use App\Models\BimbleClass;
use App\Models\ClassActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ClassActivityController extends Controller
{
    public function index(Request $request, BimbleClass $bimbleClass)
    {
        if (! Schema::hasTable('class_activities')) {
            return response()->json([]);
        }

        $this->authorizeView($request, $bimbleClass);

        $items = ClassActivity::with(['creator:id,name'])
            ->where('bimble_class_id', $bimbleClass->id)
            ->orderByDesc('happened_at')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return response()->json($items);
    }

    public function store(Request $request, BimbleClass $bimbleClass)
    {
        if (! Schema::hasTable('class_activities')) {
            return response()->json(['message' => 'Fitur aktivitas kelas belum aktif. Jalankan migrasi terbaru.'], 503);
        }

        $this->authorizeManage($request, $bimbleClass);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'happened_at' => 'nullable|date',
        ]);

        $item = ClassActivity::create([
            'bimble_class_id' => $bimbleClass->id,
            'created_by' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'happened_at' => $data['happened_at'] ?? now(),
        ]);

        return response()->json($item->load('creator:id,name'), 201);
    }

    private function authorizeView(Request $request, BimbleClass $bimbleClass): void
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';
        $isOwnerMentor = $user->role === 'mentor' && (int) $bimbleClass->created_by === (int) $user->id;
        $isStudent = $bimbleClass->students()->where('users.id', $user->id)->exists();

        if (! ($isAdmin || $isOwnerMentor || $isStudent)) {
            abort(403, 'Unauthorized');
        }
    }

    private function authorizeManage(Request $request, BimbleClass $bimbleClass): void
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            return;
        }

        if ($user->role === 'mentor' && (int) $bimbleClass->created_by === (int) $user->id) {
            return;
        }

        abort(403, 'Unauthorized');
    }
}

