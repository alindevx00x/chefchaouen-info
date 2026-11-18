<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\PlaceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Place::with('category');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('place_category_id', $request->category);
        }
        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $query->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('suggested_by_name', 'like', $term));
        }

        $places = $query->latest()->paginate(15)->withQueryString();
        $categories = PlaceCategory::orderBy('sort_order')->get();

        return view('admin.places.index', compact('places', 'categories'));
    }

    public function create()
    {
        $categories = PlaceCategory::orderBy('sort_order')->get();
        return view('admin.places.form', ['place' => new Place(['status' => 'published', 'price_range' => '$$']), 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $place = new Place();
        $this->savePlace($request, $place, true);
        return redirect()->route('admin.places.index')->with('success', 'Place created successfully.');
    }

    public function edit(Place $place)
    {
        $categories = PlaceCategory::orderBy('sort_order')->get();
        return view('admin.places.form', compact('place', 'categories'));
    }

    public function update(Request $request, Place $place)
    {
        $this->savePlace($request, $place, false);
        return redirect()->route('admin.places.index')->with('success', 'Place updated successfully.');
    }

    public function publish(Place $place)
    {
        $place->update(['status' => 'published']);
        return back()->with('success', "'{$place->title}' is now published.");
    }

    public function destroy(Place $place)
    {
        $place->delete();
        return back()->with('success', 'Place deleted successfully.');
    }

    protected function savePlace(Request $request, Place $place, bool $isNew): void
    {
        $data = $request->validate([
            'place_category_id' => 'required|exists:place_categories,id',
            'title' => 'required|string|max:180',
            'subtitle' => 'nullable|string|max:200',
            'short_description' => 'nullable|string|max:1000',
            'description' => 'nullable|string',
            'featured_image_url' => 'nullable|string|max:255',
            'featured_image_file' => 'nullable|image|max:5120',
            'album_photos_files.*' => 'nullable|image|max:5120',
            'album_photos_urls' => 'nullable|string',
            'direction_map_iframe' => 'nullable|string',
            'map_query' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'opening_hours' => 'nullable|string|max:180',
            'address' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:120',
            'phone' => 'nullable|string|max:60',
            'whatsapp' => 'nullable|string|max:60',
            'website' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:160',
            'amenities_text' => 'nullable|string',
            'highlights_text' => 'nullable|string',
            'price_range' => 'nullable|string|max:20',
            'badge_text' => 'nullable|string|max:80',
            'status' => 'required|in:published,draft,pending',
        ]);

        $featuredImage = $data['featured_image_url'] ?: ($place->featured_image ?: '/images/gallery/medina-door.jpg');
        if ($request->hasFile('featured_image_file')) {
            $featuredImage = '/storage/' . $request->file('featured_image_file')->store('uploads/places', 'public');
        }

        $album = [];
        if ($request->filled('album_photos_urls')) {
            $album = array_values(array_filter(array_map('trim', explode("\n", $data['album_photos_urls']))));
        } elseif (!$isNew && is_array($place->album_photos)) {
            $album = $place->album_photos;
        }
        if ($request->hasFile('album_photos_files')) {
            foreach ($request->file('album_photos_files') as $file) {
                $album[] = '/storage/' . $file->store('uploads/places/album', 'public');
            }
        }

        $amenities = $request->filled('amenities_text')
            ? array_values(array_filter(array_map('trim', explode(',', $data['amenities_text']))))
            : [];
        $highlights = $request->filled('highlights_text')
            ? array_values(array_filter(array_map('trim', explode(',', $data['highlights_text']))))
            : [];

        $slug = $place->slug ?: Str::slug($data['title']);
        if ($isNew && Place::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $place->fill([
            'place_category_id' => $data['place_category_id'],
            'title' => $data['title'],
            'slug' => $slug,
            'subtitle' => $data['subtitle'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'featured_image' => $featuredImage,
            'album_photos' => $album,
            'direction_map_iframe' => $data['direction_map_iframe'] ?? null,
            'map_query' => $data['map_query'] ?? $data['title'],
            'latitude' => $data['latitude'] ?? 35.1688,
            'longitude' => $data['longitude'] ?? -5.2636,
            'opening_hours' => $data['opening_hours'] ?? null,
            'address' => $data['address'] ?? null,
            'neighborhood' => $data['neighborhood'] ?? 'Old Medina',
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'website' => $data['website'] ?? null,
            'email' => $data['email'] ?? null,
            'amenities' => $amenities,
            'highlights' => $highlights,
            'price_range' => $data['price_range'] ?? '$$',
            'badge_text' => $data['badge_text'] ?? null,
            'is_verified_partner' => $request->boolean('is_verified_partner'),
            'is_featured' => $request->boolean('is_featured'),
            'is_hidden_gem' => $request->boolean('is_hidden_gem'),
            'status' => $data['status'],
        ])->save();
    }
}
