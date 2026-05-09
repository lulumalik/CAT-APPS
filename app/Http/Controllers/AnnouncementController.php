<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user && $user->role === 'admin';

        $query = Announcement::with('creator:id,name')
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if (! $isAdmin || ! $request->boolean('manage')) {
            $targetRole = $user?->role ?? 'guest';
            $query->where('is_published', true)
                ->where(function ($q) use ($targetRole) {
                    $q->where('target_role', 'all')
                        ->orWhere('target_role', $targetRole);
                });
        }

        return response()->json($query->limit(100)->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:180',
            'body' => 'required|string|max:10000',
            'target_role' => 'required|in:all,user,mentor,admin',
            'is_published' => 'nullable|boolean',
        ]);

        $announcement = Announcement::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'target_role' => $data['target_role'],
            'is_published' => (bool) ($data['is_published'] ?? true),
            'published_at' => ($data['is_published'] ?? true) ? now() : null,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($announcement->load('creator:id,name'), 201);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:180',
            'body' => 'sometimes|required|string|max:10000',
            'target_role' => 'sometimes|required|in:all,user,mentor,admin',
            'is_published' => 'nullable|boolean',
        ]);

        if (array_key_exists('is_published', $data)) {
            $data['published_at'] = $data['is_published'] ? ($announcement->published_at ?? now()) : null;
        }

        $announcement->update($data);

        return response()->json($announcement->fresh()->load('creator:id,name'));
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return response()->noContent();
    }
}
