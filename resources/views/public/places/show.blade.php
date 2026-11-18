@extends('layouts.public')

@section('title', $place->title . ' | Chefchaouen.info')
@section('meta_description', $place->short_description)

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Breadcrumb & Header -->
        <div class="mb-6 flex flex-wrap items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-chaouen-600">Home</a> /
            <a href="{{ route('places.index') }}" class="hover:text-chaouen-600">Places</a> /
            @if($place->category)
                <a href="{{ route('places.index', ['category' => $place->category->slug]) }}" class="hover:text-chaouen-600">{{ $place->category->name }}</a> /
            @endif
            <span class="text-slate-900 font-medium">{{ $place->title }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left 8 cols: Photo Gallery, Description, Map, Reviews -->
            <div class="lg:col-span-8 space-y-8">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if($place->category)
                            <span class="px-3 py-1 rounded-full bg-chaouen-100 text-chaouen-800 text-xs font-bold">{{ $place->category->name }}</span>
                        @endif
                        @if($place->is_verified_partner)
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">✓ Verified Partner</span>
                        @endif
                        @if($place->is_hidden_gem)
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">✨ Hidden Gem</span>
                        @endif
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">★ {{ number_format($place->rating_avg, 1) }} ({{ $place->reviews_count }} reviews)</span>
                    </div>
                    <h1 class="font-editorial text-4xl sm:text-5xl font-bold text-slate-900">{{ $place->title }}</h1>
                    @if($place->subtitle)
                        <p class="text-base text-slate-600 mt-2">{{ $place->subtitle }}</p>
                    @endif
                </div>

                <!-- Main Image + Album Grid -->
                <div class="space-y-3">
                    <div class="aspect-[16/9] rounded-3xl overflow-hidden bg-slate-100 shadow-sm border border-slate-200">
                        <img src="{{ asset($place->featured_image ?: '/images/gallery/medina-door.jpg') }}" alt="{{ $place->title }}" class="w-full h-full object-cover">
                    </div>
                    @if(is_array($place->album_photos) && count($place->album_photos) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($place->album_photos as $photo)
                                <div class="aspect-[4/3] rounded-2xl overflow-hidden border border-slate-200">
                                    <img src="{{ asset($photo) }}" alt="{{ $place->title }} photo" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Rich Description (TinyMCE HTML) -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 prose prose-slate max-w-none">
                    {!! $place->description ?: '<p>' . e($place->short_description) . '</p>' !!}

                    @if(is_array($place->amenities) && count($place->amenities) > 0)
                        <div class="mt-6 pt-6 border-t border-slate-100 not-prose">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-3">Amenities & Highlights</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($place->amenities as $item)
                                    <span class="px-3 py-1.5 rounded-full bg-chaouen-50 text-chaouen-800 border border-chaouen-100 text-xs font-medium">✓ {{ $item }}</span>
                                @endforeach
                                @if(is_array($place->highlights))
                                    @foreach($place->highlights as $hl)
                                        <span class="px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-medium">★ {{ $hl }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Direction Map (Google Maps Iframe) -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <div>
                            <h2 class="font-editorial text-2xl font-bold text-slate-900">Direction Map</h2>
                            <p class="text-xs text-slate-500">{{ $place->address ?: 'Chefchaouen Medina, Morocco' }}</p>
                        </div>
                        <a href="{{ $place->google_maps_external_url }}" target="_blank" rel="noopener"
                           class="px-4 py-2 rounded-full bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-semibold transition">
                            Open in Google Maps ↗
                        </a>
                    </div>
                    <div class="rounded-2xl overflow-hidden border border-slate-200">
                        {!! $place->map_iframe_html !!}
                    </div>
                </div>

                <!-- Visitor Reviews & Ratings + Submission Form -->
                @include('public.partials.reviews-section', [
                    'type' => 'place',
                    'targetId' => $place->id,
                    'reviews' => $place->approvedReviews,
                    'ratingAvg' => $place->rating_avg,
                ])
            </div>

            <!-- Right 4 cols: Contact Details & Ask AI -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm sticky top-24 space-y-5">
                    <h2 class="font-editorial text-2xl font-bold text-slate-900">Contact & Practical Info</h2>

                    <div class="space-y-3 text-sm">
                        @if($place->neighborhood)
                            <div class="flex justify-between gap-3 py-2 border-b border-slate-100">
                                <span class="text-slate-500">Neighborhood</span>
                                <span class="font-semibold text-slate-900 text-right">{{ $place->neighborhood }}</span>
                            </div>
                        @endif
                        @if($place->price_range)
                            <div class="flex justify-between gap-3 py-2 border-b border-slate-100">
                                <span class="text-slate-500">Price Range</span>
                                <span class="font-semibold text-slate-900">{{ $place->price_range }}</span>
                            </div>
                        @endif
                        @if($place->opening_hours)
                            <div class="flex justify-between gap-3 py-2 border-b border-slate-100">
                                <span class="text-slate-500">Opening Hours</span>
                                <span class="font-semibold text-slate-900 text-right">{{ $place->opening_hours }}</span>
                            </div>
                        @endif
                        @if($place->address)
                            <div class="flex justify-between gap-3 py-2 border-b border-slate-100">
                                <span class="text-slate-500">Address</span>
                                <span class="font-semibold text-slate-900 text-right">{{ $place->address }}</span>
                            </div>
                        @endif
                        @if($place->phone)
                            <div class="flex justify-between gap-3 py-2 border-b border-slate-100">
                                <span class="text-slate-500">Phone</span>
                                <a href="tel:{{ $place->phone }}" class="font-semibold text-chaouen-600 hover:underline">{{ $place->phone }}</a>
                            </div>
                        @endif
                        @if($place->email)
                            <div class="flex justify-between gap-3 py-2 border-b border-slate-100">
                                <span class="text-slate-500">Email</span>
                                <a href="mailto:{{ $place->email }}" class="font-semibold text-chaouen-600 hover:underline">{{ $place->email }}</a>
                            </div>
                        @endif
                        @if($place->website)
                            <div class="flex justify-between gap-3 py-2 border-b border-slate-100">
                                <span class="text-slate-500">Website</span>
                                <a href="{{ $place->website }}" target="_blank" rel="noopener" class="font-semibold text-chaouen-600 hover:underline truncate max-w-[180px]">Visit Website ↗</a>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2.5 pt-2">
                        @if($place->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $place->whatsapp) }}" target="_blank" rel="noopener"
                               class="w-full py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm flex items-center justify-center gap-2 transition">
                                💬 Message on WhatsApp
                            </a>
                        @endif
                        <a href="{{ $place->google_maps_external_url }}" target="_blank" rel="noopener"
                           class="w-full py-3 px-4 rounded-2xl bg-chaouen-600 hover:bg-chaouen-700 text-white font-semibold text-sm flex items-center justify-center gap-2 transition">
                            🗺️ Open in Google Maps
                        </a>
                        <button type="button"
                                @click="$dispatch('open-chaouen-chat', { prompt: @js('Tell me more about ' . $place->title . ' in Chefchaouen and nearby spots.') })"
                                class="w-full py-3 px-4 rounded-2xl bg-chaouen-50 hover:bg-chaouen-100 text-chaouen-800 border border-chaouen-200 font-semibold text-sm flex items-center justify-center gap-2 transition">
                            ✨ Ask AI About This Place
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Places in Medina (4-Column Grid) -->
        @if($similarPlaces->isNotEmpty())
            <div class="mt-16 pt-12 border-t border-slate-200">
                <h2 class="font-editorial text-3xl font-bold text-slate-900 mb-6">Similar Places in Medina</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($similarPlaces as $sim)
                        @include('public.partials.place-card', ['place' => $sim])
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
