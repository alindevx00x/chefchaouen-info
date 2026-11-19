<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ChatbotLogController;
use App\Http\Controllers\Admin\ContactNewsletterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryPhotoController;
use App\Http\Controllers\Admin\PlaceCategoryController;
use App\Http\Controllers\Admin\PlaceController;
use App\Http\Controllers\Admin\PublicPageController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TouristRouteController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Public Visitor Routes (No Admin Link Exposed in Public UI)
Route::get('/', [PublicController::class, 'home'])->name('home');

Route::get('/places', [PublicController::class, 'places'])->name('places.index');
Route::get('/places/{slug}', [PublicController::class, 'placeShow'])->name('places.show');

Route::get('/routes', [PublicController::class, 'routes'])->name('routes.index');
Route::get('/routes/{slug}', [PublicController::class, 'routeShow'])->name('routes.show');

Route::get('/map', [PublicController::class, 'interactiveMap'])->name('map.index');
Route::get('/gallery', [PublicController::class, 'gallery'])->name('gallery.index');

Route::get('/blog', [PublicController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [PublicController::class, 'blogShow'])->name('blog.show');

Route::get('/faq', [PublicController::class, 'faq'])->name('faq.index');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact.index');
Route::post('/contact', [PublicController::class, 'submitContact'])->name('contact.submit');

Route::post('/newsletter', [PublicController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');
Route::post('/reviews', [PublicController::class, 'submitReview'])->name('reviews.submit');

Route::get('/suggest', [PublicController::class, 'suggestForm'])->name('suggest.form');
Route::post('/suggest', [PublicController::class, 'submitSuggestion'])->name('suggest.submit');

Route::get('/page/{slug}', [PublicController::class, 'pageShow'])->name('page.show');

// Chefchaouen Guide AI Chatbot Endpoint
Route::post('/api/chatbot/ask', [ChatbotController::class, 'ask'])->name('chatbot.ask');

// Hidden Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Places & Categories
        Route::get('/categories', [PlaceCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [PlaceCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [PlaceCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [PlaceCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/places', [PlaceController::class, 'index'])->name('places.index');
        Route::get('/places/create', [PlaceController::class, 'create'])->name('places.create');
        Route::post('/places', [PlaceController::class, 'store'])->name('places.store');
        Route::get('/places/{place}/edit', [PlaceController::class, 'edit'])->name('places.edit');
        Route::put('/places/{place}', [PlaceController::class, 'update'])->name('places.update');
        Route::post('/places/{place}/publish', [PlaceController::class, 'publish'])->name('places.publish');
        Route::delete('/places/{place}', [PlaceController::class, 'destroy'])->name('places.destroy');

        // Tourist Routes
        Route::get('/routes', [TouristRouteController::class, 'index'])->name('routes.index');
        Route::get('/routes/create', [TouristRouteController::class, 'create'])->name('routes.create');
        Route::post('/routes', [TouristRouteController::class, 'store'])->name('routes.store');
        Route::get('/routes/{route}/edit', [TouristRouteController::class, 'edit'])->name('routes.edit');
        Route::put('/routes/{route}', [TouristRouteController::class, 'update'])->name('routes.update');
        Route::post('/routes/{route}/publish', [TouristRouteController::class, 'publish'])->name('routes.publish');
        Route::delete('/routes/{route}', [TouristRouteController::class, 'destroy'])->name('routes.destroy');

        // Public Pages (TinyMCE + PDFs)
        Route::get('/pages', [PublicPageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [PublicPageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [PublicPageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [PublicPageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [PublicPageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [PublicPageController::class, 'destroy'])->name('pages.destroy');

        // Gallery Photos
        Route::get('/gallery', [GalleryPhotoController::class, 'index'])->name('gallery.index');
        Route::post('/gallery', [GalleryPhotoController::class, 'store'])->name('gallery.store');
        Route::put('/gallery/{photo}', [GalleryPhotoController::class, 'update'])->name('gallery.update');
        Route::delete('/gallery/{photo}', [GalleryPhotoController::class, 'destroy'])->name('gallery.destroy');

        // Blog Posts & Categories
        Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
        Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
        Route::get('/blog/{post}/edit', [BlogController::class, 'edit'])->name('blog.edit');
        Route::put('/blog/{post}', [BlogController::class, 'update'])->name('blog.update');
        Route::delete('/blog/{post}', [BlogController::class, 'destroy'])->name('blog.destroy');
        Route::post('/blog-categories', [BlogController::class, 'storeCategory'])->name('blog.categories.store');
        Route::delete('/blog-categories/{category}', [BlogController::class, 'destroyCategory'])->name('blog.categories.destroy');

        // Reviews & Ratings Moderation
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
        Route::post('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Contact Submissions & Newsletters
        Route::get('/contacts', [ContactNewsletterController::class, 'contacts'])->name('contacts.index');
        Route::post('/contacts/{contact}/reply', [ContactNewsletterController::class, 'replyContact'])->name('contacts.reply');
        Route::get('/contacts/export', [ContactNewsletterController::class, 'exportContactsCsv'])->name('contacts.export');
        Route::delete('/contacts/{contact}', [ContactNewsletterController::class, 'destroyContact'])->name('contacts.destroy');

        Route::get('/newsletters', [ContactNewsletterController::class, 'newsletters'])->name('newsletters.index');
        Route::get('/newsletters/export', [ContactNewsletterController::class, 'exportNewslettersCsv'])->name('newsletters.export');
        Route::delete('/newsletters/{newsletter}', [ContactNewsletterController::class, 'destroyNewsletter'])->name('newsletters.destroy');

        // AI Chatbot Logs & Conversations Management
        Route::get('/chatbot-logs', [ChatbotLogController::class, 'index'])->name('chatbot-logs.index');
        Route::get('/chatbot-logs/export', [ChatbotLogController::class, 'exportCsv'])->name('chatbot-logs.export');
        Route::delete('/chatbot-logs/session', [ChatbotLogController::class, 'destroySession'])->name('chatbot-logs.destroy-session');
        Route::delete('/chatbot-logs/clear', [ChatbotLogController::class, 'clear'])->name('chatbot-logs.clear');
        Route::delete('/chatbot-logs/{chatbotLog}', [ChatbotLogController::class, 'destroy'])->name('chatbot-logs.destroy');

        // General Settings, Email (SMTP/Brevo), Full SEO, AI Models, FAQs
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general');
        Route::post('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.email');
        Route::post('/settings/email/test', [SettingsController::class, 'testEmail'])->name('settings.email.test');
        Route::post('/settings/seo', [SettingsController::class, 'updateSeo'])->name('settings.seo');

        Route::post('/settings/ai/custom', [SettingsController::class, 'storeCustomAiProvider'])->name('settings.ai.custom');
        Route::post('/settings/ai/{provider}', [SettingsController::class, 'saveAiProvider'])->name('settings.ai.save');
        Route::post('/settings/ai/{provider}/default', [SettingsController::class, 'setDefaultAiProvider'])->name('settings.ai.default');
        Route::post('/settings/ai/{provider}/fetch-models', [SettingsController::class, 'fetchAiModels'])->name('settings.ai.fetch');
        Route::post('/settings/ai/{provider}/test', [SettingsController::class, 'testAiProvider'])->name('settings.ai.test');

        Route::post('/settings/faqs', [SettingsController::class, 'storeFaq'])->name('settings.faqs.store');
        Route::delete('/settings/faqs/{faq}', [SettingsController::class, 'destroyFaq'])->name('settings.faqs.destroy');

        // Profile Settings & Users Management (Manage Other Admins)
        Route::get('/profile', [AdminUserController::class, 'profile'])->name('profile');
        Route::post('/profile', [AdminUserController::class, 'updateProfile'])->name('profile.update');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
