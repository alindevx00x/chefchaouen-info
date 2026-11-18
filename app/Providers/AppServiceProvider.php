<?php

namespace App\Providers;

use App\Models\PublicPage;
use App\Models\Setting;
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
