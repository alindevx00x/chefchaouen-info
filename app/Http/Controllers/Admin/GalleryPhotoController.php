<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;

class GalleryPhotoController extends Controller
{
    public function index()
    {
        $photos = GalleryPhoto::orderBy('sort_order')->latest()->get();
        return view('admin.gallery.index', compact('photos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:160',
            'category' => 'required|string|max:80',
            'location_tag' => 'nullable|string|max:120',
            'photographer' => 'nullable|string|max:120',
            'caption' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|max:8192',
            'image_url' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
        ]);

        $imagePath = $data['image_url'] ?: '/images/gallery/hero-medina.jpg';
        if ($request->hasFile('image_file')) {
            $imagePath = '/storage/' . $request->file('image_file')->store('uploads/gallery', 'public');
        }

        GalleryPhoto::create([
            'title' => $data['title'],
            'category' => $data['category'],
            'image_path' => $imagePath,
            'location_tag' => $data['location_tag'] ?? 'Chefchaouen',
            'photographer' => $data['photographer'] ?? null,
            'caption' => $data['caption'] ?? null,
            'is_featured' => $request->boolean('is_featured', true),
            'sort_order' => (GalleryPhoto::max('sort_order') ?? 0) + 1,
        ]);

        return back()->with('success', 'Photo added to Gallery Photos.');
    }

    public function update(Request $request, GalleryPhoto $photo)
    {
        $data = $request->validate([
            'title' => 'required|string|max:160',
            'category' => 'required|string|max:80',
            'location_tag' => 'nullable|string|max:120',
            'caption' => 'nullable|string|max:500',
        ]);

        $photo->update([
            'title' => $data['title'],
            'category' => $data['category'],
            'location_tag' => $data['location_tag'] ?? null,
            'caption' => $data['caption'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return back()->with('success', 'Gallery photo updated.');
    }

    public function destroy(GalleryPhoto $photo)
    {
        $photo->delete();
        return back()->with('success', 'Photo removed from gallery.');
    }
}
