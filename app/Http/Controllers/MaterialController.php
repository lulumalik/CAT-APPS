<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    // Admin/Mentor Endpoints
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Material::with('creator:id,name');

        if ($user && $user->role === 'mentor') {
            $query->where('created_by', $user->id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
        ]);
        $data['visibility'] = 'class_only';

        // Extract base64 images, save to disk, and replace with URL
        $data['content'] = $this->processContentImages($data['content']);

        $data['created_by'] = $request->user()->id;
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();

        $material = Material::create($data);

        return response()->json($material, 201);
    }

    public function showAdmin(Request $request, Material $material)
    {
        $user = $request->user();
        if ($user->role === 'mentor' && $material->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($material);
    }

    public function update(Request $request, Material $material)
    {
        $user = $request->user();
        if ($user->role === 'mentor' && $material->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
        ]);
        $data['visibility'] = 'class_only';

        // Extract base64 images, save to disk, and replace with URL
        $data['content'] = $this->processContentImages($data['content']);

        // Optional: Update slug if title changes
        if ($material->title !== $data['title']) {
            $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        }

        $material->update($data);

        return response()->json($material);
    }

    private function processContentImages($content)
    {
        if (empty($content)) {
            return $content;
        }

        $dom = new \DOMDocument();
        // Supress warnings for malformed HTML
        libxml_use_internal_errors(true);
        // Load HTML with proper encoding
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            // Check if the image is base64 encoded
            if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                $base64_str = substr($src, strpos($src, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif, etc.

                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    continue; // Skip unsupported formats
                }

                $image_data = base64_decode($base64_str);

                if ($image_data === false) {
                    continue; // Skip invalid base64
                }

                $filename = 'material_' . uniqid() . '_' . time() . '.' . $type;
                $path = public_path('storage/materials/' . $filename);

                // Ensure directory exists
                if (!file_exists(public_path('storage/materials'))) {
                    mkdir(public_path('storage/materials'), 0755, true);
                }

                // Save the image
                file_put_contents($path, $image_data);

                // Replace the src attribute with the URL
                $img->removeAttribute('src');
                $img->setAttribute('src', asset('storage/materials/' . $filename));
            }
        }

        // Save the updated HTML
        return $dom->saveHTML();
    }

    public function destroy(Request $request, Material $material)
    {
        $user = $request->user();
        if ($user->role === 'mentor' && $material->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $material->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    // Public Endpoints
    public function publicIndex()
    {
        $materials = Material::with('creator:id,name')
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->latest()
            ->get();

        return response()->json($materials);
    }

    public function publicShow(Request $request, $slug)
    {
        $material = Material::with('creator:id,name')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if (! $material->canBeViewedBy($request->user())) {
            return response()->json(['message' => 'Materi ini hanya untuk peserta kelas tertentu. Silakan masuk dengan akun yang terdaftar.'], 403);
        }

        return response()->json($material);
    }
}
