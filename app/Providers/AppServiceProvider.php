<?php

namespace App\Providers;

use App\Models\PublicPage;
use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('chatbot', fn (Request $request) => Limit::perMinute(20)->by($request->ip()));
        RateLimiter::for('contact-form', fn (Request $request) => Limit::perMinute(6)->by($request->ip()));
        RateLimiter::for('newsletter-form', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('review-form', fn (Request $request) => Limit::perMinute(8)->by($request->ip()));
        RateLimiter::for('suggest-form', fn (Request $request) => Limit::perMinute(6)->by($request->ip()));

        View::composer('*', function ($view) {
            try {
                $settings = Setting::pluck('value', 'key')->toArray();
                $headerPages = PublicPage::where('is_published', true)->where('show_in_header', true)->orderBy('sort_order')->get();
                $footerPages = PublicPage::where('is_published', true)->where('show_in_footer', true)->orderBy('sort_order')->get();
            } catch (\Throwable $e) {
                $settings = [];
                $headerPages = collect();
                $footerPages = collect();
            }

            $view->with('siteSettings', $settings);
            $view->with('headerPages', $headerPages);
            $view->with('footerPages', $footerPages);
        });
    }
}
