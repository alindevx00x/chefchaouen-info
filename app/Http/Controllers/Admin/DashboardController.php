<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use App\Models\BlogPost;
use App\Models\ChatbotLog;
use App\Models\ContactSubmission;
use App\Models\GalleryPhoto;
use App\Models\Newsletter;
use App\Models\Place;
use App\Models\PublicPage;
use App\Models\Review;
use App\Models\TouristRoute;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'places_published' => Place::where('status', 'published')->count(),
            'places_drafts' => Place::whereIn('status', ['draft', 'pending'])->count(),
            'routes_published' => TouristRoute::where('status', 'published')->count(),
            'routes_drafts' => TouristRoute::whereIn('status', ['draft', 'pending'])->count(),
            'reviews_pending' => Review::where('status', 'pending')->count(),
            'contacts_new' => ContactSubmission::where('status', 'new')->count(),
            'newsletters' => Newsletter::where('status', 'subscribed')->count(),
            'gallery_photos' => GalleryPhoto::count(),
            'blog_posts' => BlogPost::count(),
            'public_pages' => PublicPage::count(),
            'chat_queries' => ChatbotLog::count(),
        ];

        $pendingReviews = Review::with('reviewable')->where('status', 'pending')->latest()->take(5)->get();
        $suggestedPlaces = Place::with('category')->whereNotNull('suggested_by_name')->latest()->take(5)->get();
        $suggestedRoutes = TouristRoute::whereNotNull('suggested_by_name')->latest()->take(5)->get();
        $recentContacts = ContactSubmission::latest()->take(5)->get();
        $defaultAi = AiProvider::getDefault();

        return view('admin.dashboard', compact(
            'stats',
            'pendingReviews',
            'suggestedPlaces',
            'suggestedRoutes',
            'recentContacts',
            'defaultAi'
        ));
    }
}
