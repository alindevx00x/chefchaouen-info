<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlaceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlaceCategoryController extends Controller
{
    public function index()
    {
        $categories = PlaceCategory::withCount('places')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'subtitle' => 'nullable|string|max:160',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:60',
            'accent_color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'door_image_file' => 'nullable|image|max:4096',
            'door_image_url' => 'nullable|string|max:255',
        ]);

        $doorImage = $data['door_image_url'] ?? '/images/gallery/medina-door.jpg';
        if ($request->hasFile('door_image_file')) {
            $path = $request->file('door_image_file')->store('uploads/doors', 'public');
            $doorImage = '/storage/' . $path;
        }

        PlaceCategory::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(3)),
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? 'map-pin',
            'accent_color' => $data['accent_color'] ?? '#1d4ed8',
            'sort_order' => $data['sort_order'] ?? 0,
            'door_image' => $doorImage,
            'is_active' => true,
        ]);

        return back()->with('success', 'Place category created successfully.');
    }

    public function update(Request $request, PlaceCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'subtitle' => 'nullable|string|max:160',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:60',
            'accent_color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'door_image_file' => 'nullable|image|max:4096',
            'door_image_url' => 'nullable|string|max:255',
        ]);

        $doorImage = $data['door_image_url'] ?: $category->door_image;
        if ($request->hasFile('door_image_file')) {
            $path = $request->file('door_image_file')->store('uploads/doors', 'public');
            $doorImage = '/storage/' . $path;
        }

        $category->update([
            'name' => $data['name'],
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? $category->icon,
            'accent_color' => $data['accent_color'] ?? $category->accent_color,
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $category->is_active,
            'door_image' => $doorImage,
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(PlaceCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
