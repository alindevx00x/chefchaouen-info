<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactSubmission;
use App\Models\Faq;
use App\Models\GalleryPhoto;
use App\Models\Newsletter;
use App\Models\Place;
use App\Models\PlaceCategory;
use App\Models\PublicPage;
use App\Models\Review;
use App\Models\TouristRoute;
use App\Services\SeoFeedService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function sitemap(Request $request, SeoFeedService $seoFeed)
    {
        return response(
            $seoFeed->buildSitemapXml($request->getSchemeAndHttpHost()),
            200,
            ['Content-Type' => 'application/xml; charset=UTF-8']
        );
    }

    public function llms(Request $request, SeoFeedService $seoFeed)
    {
        return response(
            $seoFeed->buildLlmsTxt($request->getSchemeAndHttpHost()),
            200,
            ['Content-Type' => 'text/plain; charset=UTF-8']
        );
    }

    protected function isHoneypotTriggered(Request $request): bool
    {
        return trim((string) $request->input('website_hp', '')) !== '';
    }
    public function home()
    {
        $categories = PlaceCategory::where('is_active', true)
            ->withCount(['places' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('sort_order')
            ->get();

        $featuredPlaces = Place::with('category')
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderByDesc('rating_avg')
            ->orderBy('id')
            ->take(12)
            ->get();

        $hiddenGems = Place::with('category')
            ->where('status', 'published')
            ->where('is_hidden_gem', true)
            ->orderByDesc('rating_avg')
            ->orderBy('id')
            ->take(8)
            ->get();

        $verifiedPartners = Place::with('category')
            ->where('status', 'published')
            ->where('is_verified_partner', true)
            ->orderByDesc('rating_avg')
            ->orderBy('id')
            ->take(4)
            ->get();

        $latestRoutes = TouristRoute::where('status', 'published')
            ->orderByDesc('is_featured')
            ->orderBy('id')
            ->take(8)
            ->get();

        $galleryPhotos = GalleryPhoto::where('is_featured', true)
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $faqs = Faq::where('is_active', true)
            ->where('show_on_home', true)
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        $counts = [
            'places' => Place::where('status', 'published')->count(),
            'routes' => TouristRoute::where('status', 'published')->count(),
            'categories' => $categories->count(),
        ];

        $allMapPlaces = Place::with('category')->where('status', 'published')->get();
        $allMapRoutes = TouristRoute::where('status', 'published')->get();

        return view('public.home', compact(
            'categories',
            'featuredPlaces',
            'hiddenGems',
            'verifiedPartners',
            'latestRoutes',
            'galleryPhotos',
            'faqs',
            'counts',
            'allMapPlaces',
            'allMapRoutes'
        ));
    }

    public function places(Request $request)
    {
        $categories = PlaceCategory::where('is_active', true)
            ->withCount(['places' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('sort_order')
            ->get();

        $query = Place::with('category')->where('status', 'published');

        if ($request->filled('category')) {
            $slug = $request->string('category')->toString();
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->string('q')->toString() . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('short_description', 'like', $term)
                    ->orWhere('neighborhood', 'like', $term)
                    ->orWhere('address', 'like', $term);
            });
        }

        if ($request->boolean('hidden_gems')) {
            $query->where('is_hidden_gem', true);
        }

        if ($request->boolean('verified')) {
            $query->where('is_verified_partner', true);
        }

        $places = $query->latest()->orderByDesc('id')->paginate(16)->withQueryString();

        return view('public.places.index', compact('categories', 'places'));
    }

    public function placeShow(string $slug)
    {
        $place = Place::with(['category', 'approvedReviews'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $similarPlaces = Place::with('category')
            ->where('status', 'published')
            ->where('id', '!=', $place->id)
            ->where(function ($q) use ($place) {
                $q->where('place_category_id', $place->place_category_id)
                    ->orWhere('neighborhood', $place->neighborhood);
            })
            ->take(4)
            ->get();

        return view('public.places.show', compact('place', 'similarPlaces'));
    }

    public function routes(Request $request)
    {
        $query = TouristRoute::where('status', 'published');

        if ($request->filled('type') && in_array($request->type, ['inside_city', 'outside_city'], true)) {
            $query->where('route_type', $request->type);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('q')) {
            $term = '%' . $request->string('q')->toString() . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('title_ar', 'like', $term)
                    ->orWhere('short_description', 'like', $term)
                    ->orWhere('start_point', 'like', $term)
                    ->orWhere('end_point', 'like', $term);
            });
        }

        $routes = $query->orderBy('route_type')->latest()->get();

        return view('public.routes.index', compact('routes'));
    }

    public function routeShow(string $slug)
    {
        $route = TouristRoute::with('approvedReviews')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $otherRoutes = TouristRoute::where('status', 'published')
            ->where('id', '!=', $route->id)
            ->take(4)
            ->get();

        return view('public.routes.show', compact('route', 'otherRoutes'));
    }

    public function interactiveMap(Request $request)
    {
        $categories = PlaceCategory::where('is_active', true)
            ->withCount(['places' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('sort_order')
            ->get();

        $places = Place::with('category')
            ->where('status', 'published')
            ->latest()
            ->get();

        $routes = TouristRoute::where('status', 'published')
            ->latest()
            ->get();

        $placesCount = $places->whereIn('category.slug', ['historical-places', 'hiking-nature', 'activities'])->count();
        $directoryCount = $places->count();
        $routesCount = $routes->count();

        return view('public.map', compact(
            'categories',
            'places',
            'routes',
            'placesCount',
            'directoryCount',
            'routesCount'
        ));
    }

    public function gallery(Request $request)
    {
        $categories = GalleryPhoto::select('category')->distinct()->pluck('category');
        $query = GalleryPhoto::orderBy('sort_order');
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        $photos = $query->get();

        return view('public.gallery', compact('photos', 'categories'));
    }

    public function blog(Request $request)
    {
        $categories = BlogCategory::withCount(['posts' => fn ($q) => $q->where('is_published', true)])->get();
        $query = BlogPost::with(['category', 'author'])->where('is_published', true);

        if ($request->filled('category')) {
            $slug = $request->string('category')->toString();
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->string('q')->toString() . '%';
            $query->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('excerpt', 'like', $term));
        }

        $posts = $query->latest('published_at')->paginate(8)->withQueryString();

        return view('public.blog.index', compact('posts', 'categories'));
    }

    public function blogShow(string $slug)
    {
        $post = BlogPost::with(['category', 'author'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedPosts = BlogPost::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('public.blog.show', compact('post', 'relatedPosts'));
    }

    public function pageShow(string $slug)
    {
        $page = PublicPage::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('public.page', compact('page'));
    }

    public function faq()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();
        return view('public.faq', compact('faqs'));
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function submitContact(Request $request)
    {
        if ($this->isHoneypotTriggered($request)) {
            return back()->with('success', 'Thank you! Your message has been sent to the Chefchaouen.info editorial team.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'inquiry_type' => 'nullable|string|max:80',
            'subject' => 'required|string|max:180',
            'message' => 'required|string|min:10|max:3000',
        ]);

        ContactSubmission::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'inquiry_type' => $data['inquiry_type'] ?? 'General Inquiry',
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'new',
        ]);

        return back()->with('success', 'Thank you! Your message has been sent to the Chefchaouen.info editorial team.');
    }

    public function subscribeNewsletter(Request $request)
    {
        if ($this->isHoneypotTriggered($request)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Subscribed to Chefchaouen.info updates!']);
            }
            return back()->with('newsletter_success', 'Welcome aboard! You are now subscribed to Chefchaouen.info updates.');
        }

        $data = $request->validate([
            'email' => 'required|email|max:160',
            'name' => 'nullable|string|max:120',
        ]);

        Newsletter::updateOrCreate(
            ['email' => strtolower($data['email'])],
            [
                'name' => $data['name'] ?? null,
                'status' => 'subscribed',
                'source' => 'website',
            ]
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Subscribed to Chefchaouen.info updates!']);
        }

        return back()->with('newsletter_success', 'Welcome aboard! You are now subscribed to Chefchaouen.info updates.');
    }

    public function submitReview(Request $request)
    {
        if ($this->isHoneypotTriggered($request)) {
            return back()->with('review_success', 'Thank you! Your review & rating have been submitted and will appear once approved by our team.');
        }

        $data = $request->validate([
            'type' => 'required|in:place,route',
            'id' => 'required|integer',
            'visitor_name' => 'required|string|max:100',
            'visitor_email' => 'required|email|max:160',
            'visitor_country' => 'nullable|string|max:80',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:160',
            'comment' => 'required|string|min:5|max:2000',
        ]);

        $modelClass = $data['type'] === 'place' ? Place::class : TouristRoute::class;
        $target = $modelClass::findOrFail($data['id']);

        Review::create([
            'reviewable_type' => $modelClass,
            'reviewable_id' => $target->id,
            'visitor_name' => $data['visitor_name'],
            'visitor_email' => $data['visitor_email'],
            'visitor_country' => $data['visitor_country'] ?? null,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'],
            'status' => 'pending', // Strictly pending until admin approves!
        ]);

        return back()->with('review_success', 'Thank you! Your review & rating have been submitted and will appear once approved by our team.');
    }

    public function suggestForm()
    {
        $categories = PlaceCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('public.suggest', compact('categories'));
    }

    public function submitSuggestion(Request $request)
    {
        if ($this->isHoneypotTriggered($request)) {
            return back()->with('success', 'Thank you! Your suggestion has been submitted for editorial review.');
        }

        $kind = $request->input('suggestion_type', 'place');

        if ($kind === 'route') {
            $data = $request->validate([
                'suggested_by_name' => 'required|string|max:120',
                'suggested_by_email' => 'required|email|max:160',
                'title' => 'required|string|max:180',
                'route_type' => 'required|in:inside_city,outside_city',
                'difficulty' => 'required|in:Easy,Moderate,Challenging',
                'estimated_walking_time' => 'nullable|string|max:80',
                'start_point' => 'required|string|max:160',
                'end_point' => 'required|string|max:160',
                'short_description' => 'required|string|min:15|max:2000',
                'suggested_notes' => 'nullable|string|max:1500',
            ]);

            TouristRoute::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']) . '-' . Str::lower(Str::random(4)),
                'route_type' => $data['route_type'],
                'difficulty' => $data['difficulty'],
                'estimated_walking_time' => $data['estimated_walking_time'] ?? '1.5 hours',
                'start_point' => $data['start_point'],
                'end_point' => $data['end_point'],
                'short_description' => $data['short_description'],
                'detailed_info' => '<p>' . e($data['short_description']) . '</p>',
                'featured_image' => '/images/gallery/rif-mountains.jpg',
                'map_query' => $data['start_point'] . ' to ' . $data['end_point'] . ' Chefchaouen',
                'status' => 'draft',
                'is_featured' => false,
                'suggested_by_name' => $data['suggested_by_name'],
                'suggested_by_email' => $data['suggested_by_email'],
                'suggested_notes' => $data['suggested_notes'] ?? null,
            ]);

            return back()->with('success', 'Thank you, ' . $data['suggested_by_name'] . '! Your suggested route has been saved as a draft for admin review.');
        }

        $data = $request->validate([
            'suggested_by_name' => 'required|string|max:120',
            'suggested_by_email' => 'required|email|max:160',
            'place_category_id' => 'required|exists:place_categories,id',
            'title' => 'required|string|max:180',
            'address' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:120',
            'phone' => 'nullable|string|max:60',
            'whatsapp' => 'nullable|string|max:60',
            'website' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:160',
            'opening_hours' => 'nullable|string|max:120',
            'price_range' => 'nullable|string|max:20',
            'short_description' => 'required|string|min:15|max:2000',
            'suggested_notes' => 'nullable|string|max:1500',
        ]);

        Place::create([
            'place_category_id' => $data['place_category_id'],
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . Str::lower(Str::random(4)),
            'short_description' => $data['short_description'],
            'description' => '<p>' . e($data['short_description']) . '</p>',
            'featured_image' => '/images/gallery/medina-door.jpg',
            'address' => $data['address'] ?? 'Chefchaouen Medina',
            'neighborhood' => $data['neighborhood'] ?? 'Old Medina',
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'website' => $data['website'] ?? null,
            'email' => $data['email'] ?? null,
            'opening_hours' => $data['opening_hours'] ?? null,
            'price_range' => $data['price_range'] ?? '$$',
            'map_query' => $data['title'] . ' Chefchaouen',
            'status' => 'draft',
            'is_featured' => false,
            'is_verified_partner' => false,
            'suggested_by_name' => $data['suggested_by_name'],
            'suggested_by_email' => $data['suggested_by_email'],
            'suggested_notes' => $data['suggested_notes'] ?? null,
        ]);

        return back()->with('success', 'Thank you, ' . $data['suggested_by_name'] . '! Your local place suggestion has been submitted as a draft for admin review.');
    }
}
