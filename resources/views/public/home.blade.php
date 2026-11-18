@extends('layouts.public')

@section('content')
    <!-- 1. HERO SECTION WITH TABBED SEARCH & BACKGROUND -->
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <img src="{{ asset($siteSettings['hero_image'] ?? '/images/gallery/hero-medina.jpg') }}"
             alt="Chefchaouen Blue Medina"
             class="absolute inset-0 w-full h-full object-cover opacity-55">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-chaouen-900/55 to-slate-950/85"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="max-w-5xl mx-auto text-center">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/25 text-xs font-semibold tracking-widest uppercase text-sky-200 mb-5">
                    Welcome to the Blue Pearl of Morocco
                </span>
                <h1 class="font-editorial text-3xl sm:text-5xl lg:text-[3.5rem] font-bold tracking-tight leading-[1.1] lg:whitespace-nowrap mb-5">
                    {{ str_replace('—', '', $siteSettings['hero_title'] ?? 'Chefchaouen Deeper Than Blue') }}
                </h1>
                <p class="text-base sm:text-lg text-slate-100/90 max-w-2xl mx-auto leading-relaxed mb-10">
                    {{ str_replace('—', ' ', $siteSettings['hero_subtitle'] ?? 'Behind every blue door lies a story. Discover curated medina places, Rif Mountain routes, and our interactive map.') }}
                </p>
            </div>

            <!-- Interactive Tabbed Search Widget (Places | Tourist Routes | Interactive Map) -->
            <div class="max-w-4xl mx-auto bg-white/95 backdrop-blur-md text-slate-900 rounded-3xl shadow-2xl border border-white/40 p-4 sm:p-6"
                 x-data="{ activeTab: 'places', query: '' }">
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3.5 mb-4">
                    <button type="button" @click="activeTab = 'places'"
                            :class="activeTab === 'places' ? 'bg-chaouen-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                            class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold transition flex items-center gap-1.5">
                        🏛️ Places to Visit
                    </button>
                    <button type="button" @click="activeTab = 'routes'"
                            :class="activeTab === 'routes' ? 'bg-chaouen-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                            class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold transition flex items-center gap-1.5">
                        🥾 Tourist Routes
                    </button>
                    <button type="button" @click="activeTab = 'map'"
                            :class="activeTab === 'map' ? 'bg-chaouen-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                            class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold transition flex items-center gap-1.5">
                        🗺️ Interactive Map
                    </button>
                </div>

                <!-- Tab 1: Places Search -->
                <form x-show="activeTab === 'places'" action="{{ route('places.index') }}" method="GET" class="space-y-3">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="q" placeholder="Search riads, tagine restaurants, rooftop cafés, parking..."
                               class="flex-1 px-4 py-3.5 rounded-2xl border border-slate-200 focus:border-chaouen-500 focus:ring-2 focus:ring-chaouen-100 outline-none text-sm">
                        <select name="category" class="sm:w-52 px-4 py-3.5 rounded-2xl border border-slate-200 bg-white text-sm outline-none">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-7 py-3.5 rounded-2xl bg-chaouen-600 hover:bg-chaouen-700 text-white font-semibold text-sm transition shadow-sm">
                            Search Places
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 pt-1">
                        <span class="font-medium text-slate-700">Popular:</span>
                        <a href="{{ route('places.index', ['category' => 'restaurants']) }}" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-chaouen-50 hover:text-chaouen-700 transition">Restaurants</a>
                        <a href="{{ route('places.index', ['category' => 'hotels']) }}" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-chaouen-50 hover:text-chaouen-700 transition">Boutique Riads</a>
                        <a href="{{ route('places.index', ['hidden_gems' => 1]) }}" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-chaouen-50 hover:text-chaouen-700 transition">Hidden Gems</a>
                        <a href="{{ route('places.index', ['category' => 'parking']) }}" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-chaouen-50 hover:text-chaouen-700 transition">Medina Parking</a>
                    </div>
                </form>

                <!-- Tab 2: Routes Search -->
                <form x-show="activeTab === 'routes'" x-cloak action="{{ route('routes.index') }}" method="GET" class="space-y-3">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="q" placeholder="Search Medina Walk, Spanish Mosque, Akchour Waterfalls, Jebel El-Kelaa..."
                               class="flex-1 px-4 py-3.5 rounded-2xl border border-slate-200 focus:border-chaouen-500 focus:ring-2 focus:ring-chaouen-100 outline-none text-sm">
                        <select name="type" class="sm:w-52 px-4 py-3.5 rounded-2xl border border-slate-200 bg-white text-sm outline-none">
                            <option value="">Inside & Outside City</option>
                            <option value="inside_city">Inside City (Medina)</option>
                            <option value="outside_city">Outside City (Province)</option>
                        </select>
                        <button type="submit" class="px-7 py-3.5 rounded-2xl bg-chaouen-600 hover:bg-chaouen-700 text-white font-semibold text-sm transition shadow-sm">
                            Find Routes
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 pt-1">
                        <span class="font-medium text-slate-700">Suggested Trails:</span>
                        <a href="{{ route('routes.show', 'medina-blue-walk') }}" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-chaouen-50 hover:text-chaouen-700 transition">Medina Blue Walk</a>
                        <a href="{{ route('routes.show', 'spanish-mosque-sunset-route') }}" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-chaouen-50 hover:text-chaouen-700 transition">Spanish Mosque</a>
                        <a href="{{ route('routes.show', 'akchour-waterfalls-route') }}" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-chaouen-50 hover:text-chaouen-700 transition">Akchour Waterfalls</a>
                        <a href="{{ route('routes.show', 'jebel-el-kelaa-trail') }}" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-chaouen-50 hover:text-chaouen-700 transition">Jebel El-Kelaa</a>
                    </div>
                </form>

                <!-- Tab 3: Interactive Map Search -->
                <form x-show="activeTab === 'map'" x-cloak action="{{ route('map.index') }}" method="GET" class="space-y-3">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="q" placeholder="Pin any landmark, hotel, café, or hiking trail on the Chefchaouen map..."
                               class="flex-1 px-4 py-3.5 rounded-2xl border border-slate-200 focus:border-chaouen-500 focus:ring-2 focus:ring-chaouen-100 outline-none text-sm">
                        <button type="submit" class="px-7 py-3.5 rounded-2xl bg-chaouen-600 hover:bg-chaouen-700 text-white font-semibold text-sm transition shadow-sm">
                            Open Live Map →
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 pt-1">
                        <span>🏛️ {{ $counts['places'] }} Directory Places</span>
                        <span>•</span>
                        <span>🥾 {{ $counts['routes'] }} Mapped Routes</span>
                        <span>•</span>
                        <span>🚪 {{ $counts['categories'] }} Categories</span>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- 2. EXPLORE BY CATEGORY (ARCHED MOORISH DOORS) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-chaouen-600 mb-2">Explore by category</p>
                <h2 class="font-editorial text-3xl sm:text-4xl font-bold text-slate-900">A city of many doorways. Choose a starting point.</h2>
                <p class="text-sm text-slate-600 mt-2">The best days here usually leave room for a little detour.</p>
            </div>
            <a href="{{ route('places.index') }}" class="text-sm font-semibold text-chaouen-600 hover:text-chaouen-800 whitespace-nowrap">
                View all places →
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-9 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('places.index', ['category' => $category->slug]) }}" class="group flex flex-col items-center text-center">
                    <div class="w-full aspect-[3/4] arch-door overflow-hidden border-2 border-chaouen-200 bg-chaouen-50 shadow-sm group-hover:shadow-lg group-hover:border-chaouen-500 transition relative">
                        <img src="{{ asset($category->door_image ?: '/images/gallery/medina-door.jpg') }}"
                             alt="{{ $category->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-chaouen-950/55 via-transparent to-transparent"></div>
                        <span class="absolute bottom-2.5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full bg-white/90 text-[10px] font-bold text-chaouen-800 shadow-sm">
                            {{ $category->places_count }}
                        </span>
                    </div>
                    <span class="mt-3 text-sm font-bold text-slate-900 group-hover:text-chaouen-600 transition">{{ $category->name }}</span>
                    <span class="text-[11px] text-slate-500 line-clamp-1">{{ $category->subtitle }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 3. YOUR CITY, YOUR PACE: FEATURED EXPERIENCES CAROUSEL (4 CARDS PER SLIDE) -->
    @php $featuredSlides = $featuredPlaces->chunk(4)->values(); @endphp
    <section class="bg-white border-y border-slate-200/70 py-16"
             x-data="{ activeSlide: 0, totalSlides: {{ max(1, $featuredSlides->count()) }}, next() { this.activeSlide = (this.activeSlide + 1) % this.totalSlides; }, prev() { this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides; } }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-chaouen-600 mb-2">YOUR CITY, YOUR PACE · Places that stay with you</p>
                    <h2 class="font-editorial text-3xl sm:text-4xl font-bold text-slate-900">Featured experiences</h2>
                    <p class="text-sm text-slate-600 mt-1.5">Hand-picked landmarks, riads, and local tables that define the Blue City.</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($featuredSlides->count() > 1)
                        <div class="flex items-center gap-2">
                            <button type="button" @click="prev()" aria-label="Previous featured experiences"
                                    class="w-10 h-10 rounded-full border border-slate-200 bg-white text-slate-700 hover:bg-chaouen-600 hover:text-white hover:border-chaouen-600 transition flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button type="button" @click="next()" aria-label="Next featured experiences"
                                    class="w-10 h-10 rounded-full border border-slate-200 bg-white text-slate-700 hover:bg-chaouen-600 hover:text-white hover:border-chaouen-600 transition flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                    <a href="{{ route('places.index') }}" class="text-sm font-semibold text-chaouen-600 hover:text-chaouen-800 whitespace-nowrap ml-1">
                        View all →
                    </a>
                </div>
            </div>

            <div class="relative overflow-hidden">
                @foreach($featuredSlides as $slideIdx => $slideChunk)
                    <div x-show="activeSlide === {{ $slideIdx }}"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         @if($slideIdx > 0) x-cloak @endif
                         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($slideChunk as $place)
                            @include('public.partials.place-card', ['place' => $place])
                        @endforeach
                    </div>
                @endforeach
            </div>

            @if($featuredSlides->count() > 1)
                <div class="flex items-center justify-center gap-2 mt-8">
                    @foreach($featuredSlides as $slideIdx => $slideChunk)
                        <button type="button" @click="activeSlide = {{ $slideIdx }}"
                                :class="activeSlide === {{ $slideIdx }} ? 'w-7 bg-chaouen-600' : 'w-2.5 bg-slate-300 hover:bg-chaouen-400'"
                                aria-label="Slide {{ $slideIdx + 1 }}"
                                class="h-2.5 rounded-full transition-all duration-300"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- 4. HIDDEN GEMS OF THE MEDINA (8 CARDS IN 4-COLUMN GRID) -->
    @if($hiddenGems->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-sky-600 mb-2">Hidden gems of the medina</p>
                <h2 class="font-editorial text-3xl sm:text-4xl font-bold text-slate-900">Quiet corners most visitors walk right past.</h2>
            </div>
            <a href="{{ route('places.index', ['hidden_gems' => 1]) }}" class="text-sm font-semibold text-chaouen-600 hover:text-chaouen-800 whitespace-nowrap">
                Explore hidden gems →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($hiddenGems->take(8) as $place)
                @include('public.partials.place-card', ['place' => $place])
            @endforeach
        </div>
    </section>
    @endif

    <!-- 5. TAKE THE SCENIC WAY: SUGGESTED ROUTES (8 CARDS IN 4-COLUMN GRID) -->
    <section class="bg-gradient-to-b from-chaouen-50/70 to-white border-y border-chaouen-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-chaouen-600 mb-2">TAKE THE SCENIC WAY · A route for the way you feel</p>
                    <h2 class="font-editorial text-3xl sm:text-4xl font-bold text-slate-900">Suggested routes</h2>
                    <p class="text-sm text-slate-600 mt-1.5">Walk the medina like a local, or head for the waterfalls of Akchour. Check local conditions before you set out.</p>
                </div>
                <a href="{{ route('routes.index') }}" class="text-sm font-semibold text-chaouen-600 hover:text-chaouen-800 whitespace-nowrap">
                    All suggested routes →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($latestRoutes->take(8) as $route)
                    @include('public.partials.route-card', ['route' => $route])
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. HISTORY & CULTURE SPOTLIGHT (1471) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-chaouen-900 text-white rounded-3xl overflow-hidden shadow-xl grid grid-cols-1 lg:grid-cols-12">
            <div class="lg:col-span-7 p-8 sm:p-12 flex flex-col justify-center">
                <span class="text-xs font-bold uppercase tracking-widest text-sky-300 mb-3">✳ NOT JUST A PRETTY VIEW · Every blue wall has a story.</span>
                <h2 class="font-editorial text-3xl sm:text-4xl font-bold leading-tight mb-4">
                    History & culture: From a 15th-century fortress to a city painted in sky.
                </h2>
                <p class="text-sm sm:text-base text-chaouen-100 leading-relaxed mb-6">
                    Founded in 1471 as a mountain fortress, Chefchaouen grew when Andalusian Muslim and Jewish families arrived after 1492. Their craftsmanship, horseshoe arches, zellige, and carved cedar, shapes the lanes you walk today.
                </p>
                <div class="mb-7">
                    <p class="text-xs uppercase tracking-wider text-sky-300 font-semibold mb-2.5">Learn to read the architecture</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-medium">Horseshoe arch</span>
                        <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-medium">Zellige tilework</span>
                        <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-medium">Blue lime-wash</span>
                        <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-medium">Studded door</span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('page.show', 'history-culture') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-chaouen-900 text-sm font-bold hover:bg-sky-100 transition">
                        History →
                    </a>
                </div>
            </div>
            <div class="lg:col-span-5 relative min-h-[280px]">
                <img src="{{ asset('/images/gallery/kasbah.jpg') }}" alt="Chefchaouen Kasbah 1471" class="w-full h-full object-cover">
            </div>
        </div>
    </section>

    <!-- 7. GALLERY PHOTOS (4-COLUMN GRID) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-chaouen-600 mb-2">Follow the journey</p>
                <h2 class="font-editorial text-3xl sm:text-4xl font-bold text-slate-900">Through the Blue Lens</h2>
            </div>
            <a href="{{ route('gallery.index') }}" class="text-sm font-semibold text-chaouen-600 hover:text-chaouen-800">Open full gallery →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($galleryPhotos->take(8) as $photo)
                <a href="{{ route('gallery.index') }}" class="group relative aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 shadow-sm">
                    <img src="{{ asset($photo->image_path) }}" alt="{{ $photo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition p-4 flex flex-col justify-end text-white">
                        <span class="text-[11px] uppercase tracking-wider text-sky-300 font-semibold">{{ $photo->category }}</span>
                        <span class="text-sm font-bold">{{ $photo->title }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 8. LOCAL PARTNERS + INTERACTIVE MAP PREVIEW + AI LOCAL GUIDE -->
    <section class="bg-white border-y border-slate-200/80 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            <!-- Featured Local Businesses -->
            <div>
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-chaouen-600 mb-2">Local partners</p>
                        <h2 class="font-editorial text-3xl sm:text-4xl font-bold text-slate-900">Featured local businesses</h2>
                    </div>
                    <a href="{{ route('places.index', ['verified' => 1]) }}" class="text-sm font-semibold text-chaouen-600 hover:text-chaouen-800">All verified partners →</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($verifiedPartners as $place)
                        @include('public.partials.place-card', ['place' => $place])
                    @endforeach
                </div>
            </div>

            <!-- Interactive Map & AI Local Guide Dual Spotlight -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <!-- Interactive Map Card -->
                <div class="lg:col-span-7 bg-skywash rounded-3xl border border-slate-200 p-6 sm:p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-widest text-chaouen-600">Interactive Google Map</span>
                                <h3 class="font-editorial text-2xl sm:text-3xl font-bold text-slate-900 mt-1">Explore Chefchaouen & Rif Trails</h3>
                            </div>
                            <a href="{{ route('map.index') }}" class="px-4 py-2 rounded-full bg-chaouen-600 text-white text-xs font-semibold hover:bg-chaouen-700 transition">
                                Fullscreen Map →
                            </a>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4 text-xs font-semibold">
                            <span class="px-3 py-1.5 rounded-full bg-white border border-slate-200 text-slate-700">🏛️ Places ({{ $allMapPlaces->count() }})</span>
                            <span class="px-3 py-1.5 rounded-full bg-white border border-slate-200 text-slate-700">🏨 Directory ({{ $counts['places'] }})</span>
                            <span class="px-3 py-1.5 rounded-full bg-white border border-slate-200 text-slate-700">🥾 Routes ({{ $allMapRoutes->count() }})</span>
                        </div>
                    </div>
                    <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 h-72">
                        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                                src="https://maps.google.com/maps?q=Chefchaouen%20Medina%20Morocco&t=m&z=14&ie=UTF8&iwloc=&output=embed"
                                class="w-full h-full"></iframe>
                    </div>
                </div>

                <!-- AI Local Guide Card -->
                <div class="lg:col-span-5 bg-gradient-to-br from-chaouen-800 to-sky-700 text-white rounded-3xl p-8 flex flex-col justify-between shadow-lg">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-sky-200 text-xs font-semibold mb-4">
                            ✨ A LOCAL GUIDE, ALWAYS IN YOUR POCKET
                        </span>
                        <h3 class="font-editorial text-3xl font-bold leading-snug mb-3">
                            Need a little local context? Ask a question. Start a better day.
                        </h3>
                        <p class="text-sm text-chaouen-100 leading-relaxed mb-6">
                            Curious about something? Ask about the medina, a slow afternoon or a walk into the Rif. Our travel assistant uses this guide’s own place and route information to help you plan.
                        </p>
                        <div class="space-y-2 mb-6">
                            <button type="button" @click="$dispatch('open-chaouen-chat', { prompt: 'Suggest a peaceful half-day itinerary in Chefchaouen medina' })"
                                    class="w-full text-left px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-medium transition">
                                “Suggest a peaceful half-day itinerary in the medina” →
                            </button>
                            <button type="button" @click="$dispatch('open-chaouen-chat', { prompt: 'How do I get to Akchour Waterfalls and how long is the hike?' })"
                                    class="w-full text-left px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-medium transition">
                                “How do I get to Akchour Waterfalls and how long is the hike?” →
                            </button>
                        </div>
                    </div>
                    <button type="button" @click="$dispatch('open-chaouen-chat')"
                            class="w-full py-3.5 px-5 rounded-2xl bg-white text-chaouen-900 font-bold text-sm hover:bg-sky-50 transition shadow">
                        Ask the travel assistant
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. FAQs + SUGGEST CTA + NEWSLETTER -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- FAQs -->
            <div class="lg:col-span-7">
                <p class="text-xs font-bold uppercase tracking-widest text-chaouen-600 mb-2">Common Questions</p>
                <h2 class="font-editorial text-3xl font-bold text-slate-900 mb-6">Frequently Asked Questions</h2>
                <div class="space-y-3" x-data="{ openFaq: 0 }">
                    @foreach($faqs as $idx => $faq)
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                            <button type="button" @click="openFaq = openFaq === {{ $idx }} ? null : {{ $idx }}"
                                    class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-semibold text-sm text-slate-900">
                                <span>{{ $faq->question }}</span>
                                <span class="text-chaouen-600 text-lg" x-text="openFaq === {{ $idx }} ? '−' : '+'"></span>
                            </button>
                            <div x-show="openFaq === {{ $idx }}" x-cloak class="px-5 pb-4 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                                {{ $faq->answer }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('faq.index') }}" class="text-sm font-semibold text-chaouen-600 hover:underline">View all FAQs →</a>
                </div>
            </div>

            <!-- Suggest Place CTA + Newsletter -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-amber-50/90 border border-amber-200/80 rounded-3xl p-7">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-amber-800">MADE WITH CARE, FOR THE PEOPLE WHO CALL IT HOME</span>
                    <h3 class="font-editorial text-2xl font-bold text-slate-900 mt-2 mb-2">Have a place in the blue city?</h3>
                    <p class="text-sm text-slate-700 leading-relaxed mb-5">
                        Know a hidden café, family riad, artisan workshop, or local Rif trail? Submit it for our editorial team to review and publish.
                    </p>
                    <a href="{{ route('suggest.form') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 text-white text-xs font-semibold hover:bg-chaouen-700 transition">
                        Suggest a Place or Route →
                    </a>
                </div>

                <div class="bg-white border border-slate-200 rounded-3xl p-7 shadow-sm">
                    <h3 class="font-editorial text-2xl font-bold text-slate-900 mb-1.5">Blue Pearl Dispatch</h3>
                    <p class="text-xs text-slate-600 mb-4">Seasonal trail tips, new hidden gems, and Chefchaouen stories, no spam.</p>
                    @if(session('newsletter_success'))
                        <div class="mb-3 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-medium">
                            {{ session('newsletter_success') }}
                        </div>
                    @endif
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-2.5">
                        @csrf
                        <input type="text" name="name" placeholder="Your name (optional)"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                        <div class="flex gap-2">
                            <input type="email" name="email" required placeholder="your@email.com"
                                   class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-sm font-semibold transition">
                                Subscribe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
