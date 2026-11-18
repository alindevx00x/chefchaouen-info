<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TouristRouteController extends Controller
{
    public function index(Request $request)
    {
        $query = TouristRoute::query();

        if ($request->filled('route_type')) {
            $query->where('route_type', $request->route_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $query->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('suggested_by_name', 'like', $term));
        }

        $routes = $query->latest()->paginate(15)->withQueryString();
        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('admin.routes.form', [
            'route' => new TouristRoute(['route_type' => 'inside_city', 'difficulty' => 'Easy', 'status' => 'published', 'is_featured' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $route = new TouristRoute();
        $this->saveRoute($request, $route, true);
        return redirect()->route('admin.routes.index')->with('success', 'Tourist route created successfully.');
    }

    public function edit(TouristRoute $route)
    {
        return view('admin.routes.form', compact('route'));
    }

    public function update(Request $request, TouristRoute $route)
    {
        $this->saveRoute($request, $route, false);
        return redirect()->route('admin.routes.index')->with('success', 'Tourist route updated successfully.');
    }

    public function publish(TouristRoute $route)
    {
        $route->update(['status' => 'published']);
        return back()->with('success', "Route '{$route->title}' is now published.");
    }

    public function destroy(TouristRoute $route)
    {
        $route->delete();
        return back()->with('success', 'Tourist route deleted.');
    }

    protected function saveRoute(Request $request, TouristRoute $route, bool $isNew): void
    {
        $data = $request->validate([
            'title' => 'required|string|max:180',
            'title_ar' => 'nullable|string|max:180',
            'route_type' => 'required|in:inside_city,outside_city',
            'difficulty' => 'required|in:Easy,Moderate,Challenging',
            'estimated_walking_time' => 'nullable|string|max:80',
            'distance_km' => 'nullable|string|max:60',
            'elevation_gain' => 'nullable|string|max:60',
            'start_point' => 'nullable|string|max:180',
            'end_point' => 'nullable|string|max:180',
            'best_season' => 'nullable|string|max:160',
            'short_description' => 'nullable|string|max:1000',
            'detailed_info' => 'nullable|string',
            'featured_image_url' => 'nullable|string|max:255',
            'featured_image_file' => 'nullable|image|max:5120',
            'album_photos_files.*' => 'nullable|image|max:5120',
            'album_photos_urls' => 'nullable|string',
            'map_iframe' => 'nullable|string',
            'map_query' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'waypoints_text' => 'nullable|string',
            'highlights_text' => 'nullable|string',
            'what_to_bring_text' => 'nullable|string',
            'ai_prompt_question' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft,pending',
        ]);

        $featuredImage = $data['featured_image_url'] ?: ($route->featured_image ?: '/images/gallery/rif-mountains.jpg');
        if ($request->hasFile('featured_image_file')) {
            $featuredImage = '/storage/' . $request->file('featured_image_file')->store('uploads/routes', 'public');
        }

        $album = [];
        if ($request->filled('album_photos_urls')) {
            $album = array_values(array_filter(array_map('trim', explode("\n", $data['album_photos_urls']))));
        } elseif (!$isNew && is_array($route->album_photos)) {
            $album = $route->album_photos;
        }
        if ($request->hasFile('album_photos_files')) {
            foreach ($request->file('album_photos_files') as $file) {
                $album[] = '/storage/' . $file->store('uploads/routes/album', 'public');
            }
        }

        $slug = $route->slug ?: Str::slug($data['title']);
        if ($isNew && TouristRoute::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $route->fill([
            'title' => $data['title'],
            'title_ar' => $data['title_ar'] ?? null,
            'slug' => $slug,
            'route_type' => $data['route_type'],
            'difficulty' => $data['difficulty'],
            'estimated_walking_time' => $data['estimated_walking_time'] ?? null,
            'distance_km' => $data['distance_km'] ?? null,
            'elevation_gain' => $data['elevation_gain'] ?? null,
            'start_point' => $data['start_point'] ?? null,
            'end_point' => $data['end_point'] ?? null,
            'best_season' => $data['best_season'] ?? 'Year-round',
            'short_description' => $data['short_description'] ?? null,
            'detailed_info' => $data['detailed_info'] ?? null,
            'featured_image' => $featuredImage,
            'album_photos' => $album,
            'map_iframe' => $data['map_iframe'] ?? null,
            'map_query' => $data['map_query'] ?? trim(($data['start_point'] ?? '') . ' to ' . ($data['end_point'] ?? '')),
            'latitude' => $data['latitude'] ?? 35.1688,
            'longitude' => $data['longitude'] ?? -5.2636,
            'waypoints' => $request->filled('waypoints_text') ? array_values(array_filter(array_map('trim', explode(',', $data['waypoints_text'])))) : [],
            'trail_highlights' => $request->filled('highlights_text') ? array_values(array_filter(array_map('trim', explode(',', $data['highlights_text'])))) : [],
            'what_to_bring' => $request->filled('what_to_bring_text') ? array_values(array_filter(array_map('trim', explode(',', $data['what_to_bring_text'])))) : [],
            'ai_prompt_question' => $data['ai_prompt_question'] ?: 'How do I hike ' . $data['title'] . '?',
            'is_featured' => $request->boolean('is_featured'),
            'status' => $data['status'],
        ])->save();
    }
}
