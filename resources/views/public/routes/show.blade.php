@extends('layouts.public')

@section('title', $route->title . ' | Tourist Routes | Chefchaouen.info')
@section('meta_description', $route->short_description)
@section('meta_keywords', implode(', ', array_filter([$route->title, $route->title_ar, 'Chefchaouen hiking route', $route->route_type_label, $route->start_point, $route->end_point, 'Rif Mountains'])))
@section('og_type', 'article')
@section('og_image', asset($route->featured_image ?: '/images/gallery/rif-mountains.jpg'))
@if($route->latitude && $route->longitude)
    @section('geo_position', $route->latitude . ';' . $route->longitude)
    @section('geo_icbm', $route->latitude . ', ' . $route->longitude)
@endif

@push('schema')
    @php
        $routeSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Tourist Routes', 'item' => route('routes.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $route->title, 'item' => route('routes.show', $route->slug)],
                    ],
                ],
                [
                    '@type' => 'TouristTrip',
                    'name' => $route->title,
                    'description' => $route->short_description,
                    'url' => route('routes.show', $route->slug),
                    'image' => asset($route->featured_image ?: '/images/gallery/rif-mountains.jpg'),
                    'touristType' => [$route->route_type_label, 'Hikers', 'Cultural Travelers'],
                    'itinerary' => [
                        '@type' => 'ItemList',
                        'itemListElement' => array_map(fn ($wp, $i) => [
                            '@type' => 'ListItem',
                            'position' => $i + 1,
                            'name' => $wp,
                        ], is_array($route->waypoints) && count($route->waypoints) > 0 ? $route->waypoints : array_filter([$route->start_point, $route->end_point]), array_keys(is_array($route->waypoints) && count($route->waypoints) > 0 ? $route->waypoints : array_filter([$route->start_point, $route->end_point]))),
                    ],
                    'aggregateRating' => [
                        '@type' => 'AggregateRating',
                        'ratingValue' => number_format($route->rating_avg, 1),
                        'reviewCount' => max(1, (int) $route->reviews_count),
                    ],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($routeSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-6 flex flex-wrap items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-chaouen-600">Home</a> /
            <a href="{{ route('routes.index') }}" class="hover:text-chaouen-600">Tourist Routes</a> /
            <span class="text-slate-900 font-medium">{{ $route->title }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-8 space-y-8">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full {{ $route->route_type === 'outside_city' ? 'bg-emerald-100 text-emerald-800' : 'bg-chaouen-100 text-chaouen-800' }} text-xs font-bold">
                            {{ $route->route_type_label }}
                        </span>
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-bold">Difficulty: {{ $route->difficulty }}</span>
                        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-bold">⏱ {{ $route->estimated_walking_time }}</span>
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">★ {{ number_format($route->rating_avg, 1) }}</span>
                    </div>
                    <h1 class="font-editorial text-4xl sm:text-5xl font-bold text-slate-900">
                        {{ $route->title }}
                        @if($route->title_ar)
                            <span class="text-2xl sm:text-3xl text-chaouen-700 font-normal ml-2">({{ $route->title_ar }})</span>
                        @endif
                    </h1>
                    <p class="text-base text-slate-600 mt-2.5 leading-relaxed">{{ $route->short_description }}</p>
                </div>

                <!-- Clickable Photo Gallery & Lightbox -->
                @include('public.partials.media-gallery', [
                    'featured' => $route->featured_image ?: '/images/gallery/rif-mountains.jpg',
                    'album' => $route->album_photos ?? [],
                    'title' => $route->title,
                ])

                <!-- Google Map From -> To -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div>
                            <h2 class="font-editorial text-2xl font-bold text-slate-900">Route on Google Maps (From → To)</h2>
                            <p class="text-xs text-slate-600 mt-0.5">
                                <strong>Start:</strong> {{ $route->start_point ?: 'Chefchaouen' }} → <strong>End:</strong> {{ $route->end_point ?: 'Destination' }}
                            </p>
                        </div>
                        <a href="{{ $route->google_maps_external_url }}" target="_blank" rel="noopener"
                           class="px-4 py-2 rounded-full bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-semibold transition">
                            Open Directions in Google Maps ↗
                        </a>
                    </div>
                    <div class="rounded-2xl overflow-hidden border border-slate-200">
                        {!! $route->map_iframe_html !!}
                    </div>
                </div>

                <!-- Detailed Route Info (TinyMCE HTML) -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 prose prose-slate max-w-none">
                    {!! $route->detailed_info ?: '<p>' . e($route->short_description) . '</p>' !!}
                </div>

                <!-- Reviews & Ratings -->
                @include('public.partials.reviews-section', [
                    'type' => 'route',
                    'targetId' => $route->id,
                    'reviews' => $route->approvedReviews,
                    'ratingAvg' => $route->rating_avg,
                ])
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm sticky top-24 space-y-5">
                    <h2 class="font-editorial text-2xl font-bold text-slate-900">Trail Specifications</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Route Type</span>
                            <span class="font-semibold text-slate-900">{{ $route->route_type_label }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Difficulty</span>
                            <span class="font-semibold text-slate-900">{{ $route->difficulty }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Estimated Walking Time</span>
                            <span class="font-semibold text-chaouen-700">{{ $route->estimated_walking_time }}</span>
                        </div>
                        @if($route->distance_km)
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-500">Distance</span>
                                <span class="font-semibold text-slate-900">{{ $route->distance_km }}</span>
                            </div>
                        @endif
                        @if($route->elevation_gain)
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-500">Elevation Gain</span>
                                <span class="font-semibold text-slate-900">{{ $route->elevation_gain }}</span>
                            </div>
                        @endif
                        @if($route->best_season)
                            <div class="flex justify-between gap-2 py-2 border-b border-slate-100">
                                <span class="text-slate-500">Best Season</span>
                                <span class="font-semibold text-slate-900 text-right">{{ $route->best_season }}</span>
                            </div>
                        @endif
                    </div>

                    @if(is_array($route->waypoints) && count($route->waypoints) > 0)
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Waypoints</h3>
                            <ol class="space-y-1.5 text-xs text-slate-700">
                                @foreach($route->waypoints as $i => $wp)
                                    <li class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full bg-chaouen-100 text-chaouen-800 font-bold flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                                        <span>{{ $wp }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    @if(is_array($route->what_to_bring) && count($route->what_to_bring) > 0)
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">What to Bring</h3>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($route->what_to_bring as $gear)
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">{{ $gear }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Prominent Ask AI Button -->
                    <button type="button"
                            @click="$dispatch('open-chaouen-chat', { prompt: @js($route->ai_prompt_question ?: 'How do I hike ' . $route->title . '?') })"
                            class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-chaouen-700 to-sky-600 text-white font-bold text-sm shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                        <span>✨ Ask AI: "How do I hike this?"</span>
                    </button>
                </div>
            </div>
        </div>

        @if($otherRoutes->isNotEmpty())
            <div class="mt-16 pt-12 border-t border-slate-200">
                <h2 class="font-editorial text-3xl font-bold text-slate-900 mb-6">More Suggested Routes</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($otherRoutes as $or)
                        @include('public.partials.route-card', ['route' => $or])
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
