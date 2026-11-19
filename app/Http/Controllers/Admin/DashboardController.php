<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use App\Models\BlogPost;
use App\Models\ChatbotLog;
use App\Models\ContactSubmission;
use App\Models\Faq;
use App\Models\GalleryPhoto;
use App\Models\Newsletter;
use App\Models\Place;
use App\Models\PlaceCategory;
use App\Models\PublicPage;
use App\Models\Review;
use App\Models\TouristRoute;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'places_published' => Place::where('status', 'published')->count(),
            'places_drafts' => Place::whereIn('status', ['draft', 'pending'])->count(),
            'places_featured' => Place::where('status', 'published')->where('is_featured', true)->count(),
            'places_hidden_gems' => Place::where('status', 'published')->where('is_hidden_gem', true)->count(),
            'places_verified' => Place::where('status', 'published')->where('is_verified_partner', true)->count(),
            'avg_place_rating' => round((float) (Place::where('status', 'published')->avg('rating_avg') ?? 0), 2),
            'routes_published' => TouristRoute::where('status', 'published')->count(),
            'routes_drafts' => TouristRoute::whereIn('status', ['draft', 'pending'])->count(),
            'routes_inside' => TouristRoute::where('status', 'published')->where('route_type', 'inside_city')->count(),
            'routes_outside' => TouristRoute::where('status', 'published')->where('route_type', 'outside_city')->count(),
            'reviews_approved' => Review::where('status', 'approved')->count(),
            'reviews_pending' => Review::where('status', 'pending')->count(),
            'reviews_rejected' => Review::where('status', 'rejected')->count(),
            'contacts_new' => ContactSubmission::where('status', 'new')->count(),
            'contacts_total' => ContactSubmission::count(),
            'newsletters' => Newsletter::where('status', 'subscribed')->count(),
            'gallery_photos' => GalleryPhoto::count(),
            'blog_posts' => BlogPost::count(),
            'public_pages' => PublicPage::count(),
            'faqs_count' => Faq::where('is_active', true)->count(),
            'chat_queries' => ChatbotLog::count(),
            'chat_blocked' => ChatbotLog::where('was_blocked_off_topic', true)->count(),
            'chat_allowed' => ChatbotLog::where('was_blocked_off_topic', false)->count(),
            'categories_count' => PlaceCategory::where('is_active', true)->count(),
        ];

        $categoryStats = PlaceCategory::where('is_active', true)
            ->withCount(['places as published_count' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('sort_order')
            ->get();

        $routeDifficultyStats = [
            'Easy' => TouristRoute::where('status', 'published')->where('difficulty', 'Easy')->count(),
            'Moderate' => TouristRoute::where('status', 'published')->where('difficulty', 'Moderate')->count(),
            'Challenging' => TouristRoute::where('status', 'published')->where('difficulty', 'Challenging')->count(),
        ];

        $neighborhoodStats = Place::where('status', 'published')
            ->selectRaw('neighborhood, COUNT(*) as total')
            ->groupBy('neighborhood')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        // 7-day engagement trend (database-agnostic across SQLite & MySQL)
        $days = collect(range(6, 0))->map(fn ($offset) => Carbon::today()->subDays($offset));
        $trendLabels = $days->map(fn (Carbon $d) => $d->format('M d'))->values();
        $trendStart = Carbon::today()->subDays(6)->startOfDay();

        $chatByDay = ChatbotLog::where('created_at', '>=', $trendStart)->get(['created_at'])
            ->groupBy(fn ($r) => $r->created_at->format('Y-m-d'));
        $reviewsByDay = Review::where('created_at', '>=', $trendStart)->get(['created_at'])
            ->groupBy(fn ($r) => $r->created_at->format('Y-m-d'));
        $contactsByDay = ContactSubmission::where('created_at', '>=', $trendStart)->get(['created_at'])
            ->groupBy(fn ($r) => $r->created_at->format('Y-m-d'));
        $newslettersByDay = Newsletter::where('created_at', '>=', $trendStart)->get(['created_at'])
            ->groupBy(fn ($r) => $r->created_at->format('Y-m-d'));

        $activityTrend = [
            'labels' => $trendLabels,
            'chats' => $days->map(fn (Carbon $d) => ($chatByDay[$d->format('Y-m-d')] ?? collect())->count())->values(),
            'reviews' => $days->map(fn (Carbon $d) => ($reviewsByDay[$d->format('Y-m-d')] ?? collect())->count())->values(),
            'contacts' => $days->map(fn (Carbon $d) => ($contactsByDay[$d->format('Y-m-d')] ?? collect())->count())->values(),
            'newsletters' => $days->map(fn (Carbon $d) => ($newslettersByDay[$d->format('Y-m-d')] ?? collect())->count())->values(),
        ];

        $pendingReviews = Review::with('reviewable')->where('status', 'pending')->latest()->take(5)->get();
        $suggestedPlaces = Place::with('category')->whereNotNull('suggested_by_name')->latest()->take(5)->get();
        $suggestedRoutes = TouristRoute::whereNotNull('suggested_by_name')->latest()->take(5)->get();
        $recentContacts = ContactSubmission::latest()->take(5)->get();
        $recentChatLogs = ChatbotLog::latest()->take(6)->get();
        $topRatedPlaces = Place::with('category')
            ->where('status', 'published')
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_count')
            ->take(6)
            ->get();
        $defaultAi = AiProvider::getDefault();
        $activeAiCount = AiProvider::where('is_active', true)->count();

        return view('admin.dashboard', compact(
            'stats',
            'categoryStats',
            'routeDifficultyStats',
            'neighborhoodStats',
            'activityTrend',
            'pendingReviews',
            'suggestedPlaces',
            'suggestedRoutes',
            'recentContacts',
            'recentChatLogs',
            'topRatedPlaces',
            'defaultAi',
            'activeAiCount'
        ));
    }
}
