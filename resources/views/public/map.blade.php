@extends('layouts.public')

@section('title', 'Interactive Map of Chefchaouen | Blue Medina Neighborhoods, Landmarks & Rif Routes')
@section('meta_description', 'Explore the live interactive Google Map of Chefchaouen, Morocco (35.1688 N, -5.2636 W). Locate Place Outa el-Hammam, Derb El-Asri, the 1471 Kasbah, Ras El Maa, parking gates, boutique riads, and Akchour hiking trails.')
@section('meta_keywords', 'Chefchaouen interactive map, Blue Medina map Morocco, Place Outa el-Hammam map, Derb El-Asri location, Chefchaouen parking gates, Ras El Maa, Spanish Mosque sunset point, Akchour Waterfalls trail map')

@push('schema')
    @php
        $mapSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Interactive Map', 'item' => route('map.index')],
                    ],
                ],
                [
                    '@type' => 'TouristDestination',
                    'name' => 'Chefchaouen Interactive Map & Blue Medina Guide',
                    'description' => 'Comprehensive interactive map covering Chefchaouen Old Medina squares, historic quarters, verified riads, restaurants, parking zones, and Rif Mountain trails.',
                    'url' => route('map.index'),
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => 35.1688,
                        'longitude' => -5.2636,
                    ],
                    'hasMap' => route('map.index'),
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => [
                        [
                            '@type' => 'Question',
                            'name' => 'Can cars drive inside Chefchaouen Old Medina?',
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => 'No. The historic Old Medina of Chefchaouen is completely car-free and explored exclusively on foot. Visitors arriving by car should park just outside the walls at Place El Makhzen (near Hotel Parador), Bab El Ain, or Ras El Maa and walk 2 to 10 minutes into the blue lanes.',
                            ],
                        ],
                        [
                            '@type' => 'Question',
                            'name' => 'Where is the main central square on the Chefchaouen map?',
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => 'Place Outa el-Hammam is the heart of the Old Medina (connected by six historic streets), situated right beside the 1471 Kasbah Museum and the octagonal Grand Mosque minaret.',
                            ],
                        ],
                        [
                            '@type' => 'Question',
                            'name' => 'How far is Akchour Waterfalls from Chefchaouen center?',
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => 'Akchour and Pont de Dieu lie approximately 45 minutes northeast of Chefchaouen in Talassemtane National Park. Shared grand taxis depart near town for around 20 MAD per person each way.',
                            ],
                        ],
                        [
                            '@type' => 'Question',
                            'name' => 'How long does it take to walk up to the Spanish Mosque from Ras El Maa?',
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => 'Walking from the Ras El Maa spring and Portuguese Bridge up the paved hillside path to Bouzaafar Spanish Mosque takes roughly 20 to 30 minutes at a relaxed pace and offers the best sunset panorama over the Blue Pearl.',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($mapSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    @php
        $mapItems = collect();
        foreach ($places as $p) {
            $isLandmark = in_array($p->category->slug ?? '', ['historical-places', 'hiking-nature', 'activities'], true);
            $mapItems->push([
                'uid' => 'place-' . $p->id,
                'kind' => $isLandmark ? 'places' : 'directory',
                'is_directory' => true,
                'category' => $p->category->name ?? 'Place',
                'category_slug' => $p->category->slug ?? '',
                'title' => $p->title,
                'subtitle' => $p->neighborhood ?: 'Chefchaouen',
                'description' => $p->short_description,
                'badge1' => $p->category->name ?? 'Place',
                'badge2' => $p->price_range ?: '$$',
                'badge3' => '★ ' . number_format($p->rating_avg, 1),
                'image' => asset($p->featured_image ?: '/images/gallery/medina-door.jpg'),
                'map_query' => ($p->map_query ?: $p->title) . ' Chefchaouen Morocco',
                'url' => route('places.show', $p->slug),
                'external_map_url' => $p->google_maps_external_url,
            ]);
        }
        foreach ($routes as $r) {
            $mapItems->push([
                'uid' => 'route-' . $r->id,
                'kind' => 'routes',
                'is_directory' => false,
                'category' => $r->route_type === 'outside_city' ? 'Outside City Route' : 'Inside City Route',
                'category_slug' => 'routes',
                'title' => $r->title,
                'subtitle' => ($r->start_point ?: 'Chefchaouen') . ' → ' . ($r->end_point ?: 'Summit'),
                'description' => $r->short_description,
                'badge1' => $r->route_type === 'outside_city' ? 'Outside City' : 'Inside Medina',
                'badge2' => $r->difficulty,
                'badge3' => '⏱ ' . ($r->estimated_walking_time ?: '2h'),
                'image' => asset($r->featured_image ?: '/images/gallery/rif-mountains.jpg'),
                'map_query' => ($r->map_query ?: $r->title) . ' Chefchaouen Morocco',
                'url' => route('routes.show', $r->slug),
                'external_map_url' => $r->google_maps_external_url,
            ]);
        }
    @endphp

    <section class="bg-chaouen-900 text-white py-8 border-b border-chaouen-800"
             x-data="{
                items: @js($mapItems->values()),
                mode: 'all',
                categoryFilter: '',
                search: @js(request('q', '')),
                selected: null,
                init() {
                    if (this.filteredItems().length > 0) {
                        this.selected = this.filteredItems()[0];
                    }
                },
                filteredItems() {
                    const q = this.search.toLowerCase().trim();
                    return this.items.filter(item => {
                        if (this.mode === 'places' && item.kind !== 'places') return false;
                        if (this.mode === 'directory' && !item.is_directory) return false;
                        if (this.mode === 'routes' && item.kind !== 'routes') return false;
                        if (this.categoryFilter && item.category_slug !== this.categoryFilter) return false;
                        if (q && !(item.title + ' ' + item.subtitle + ' ' + item.category + ' ' + item.description).toLowerCase().includes(q)) return false;
                        return true;
                    });
                },
                selectItem(item) {
                    this.selected = item;
                },
                getIframeSrc() {
                    if (this.search.trim().length > 2 && this.filteredItems().length === 0) {
                        return 'https://maps.google.com/maps?q=' + encodeURIComponent(this.search + ' Chefchaouen Morocco') + '&t=m&z=14&ie=UTF8&iwloc=&output=embed';
                    }
                    const q = this.selected ? this.selected.map_query : 'Chefchaouen Morocco';
                    return 'https://maps.google.com/maps?q=' + encodeURIComponent(q) + '&t=m&z=15&ie=UTF8&iwloc=&output=embed';
                },
                jumpToLandmark(keyword) {
                    this.mode = 'all';
                    this.categoryFilter = '';
                    this.search = keyword;
                    if (this.filteredItems().length > 0) {
                        this.selected = this.filteredItems()[0];
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
             }"
             @map-focus-query.window="jumpToLandmark($event.detail)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-sky-300">Live Google Maps Explorer</span>
                    <h1 class="font-editorial text-3xl sm:text-4xl font-bold mt-1">Chefchaouen Interactive Map</h1>
                </div>

                <!-- Primary Filter Badges: Places, Directory, Routes -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="mode = 'all'; categoryFilter = ''; if(filteredItems()[0]) selected = filteredItems()[0]"
                            :class="mode === 'all' ? 'bg-white text-chaouen-900' : 'bg-white/10 text-white hover:bg-white/20'"
                            class="px-4 py-2 rounded-full text-xs font-bold transition">
                        All ({{ $mapItems->count() }})
                    </button>
                    <button type="button" @click="mode = 'places'; categoryFilter = ''; if(filteredItems()[0]) selected = filteredItems()[0]"
                            :class="mode === 'places' ? 'bg-white text-chaouen-900' : 'bg-white/10 text-white hover:bg-white/20'"
                            class="px-4 py-2 rounded-full text-xs font-bold transition">
                        🏛️ Places ({{ $placesCount }})
                    </button>
                    <button type="button" @click="mode = 'directory'; categoryFilter = ''; if(filteredItems()[0]) selected = filteredItems()[0]"
                            :class="mode === 'directory' ? 'bg-white text-chaouen-900' : 'bg-white/10 text-white hover:bg-white/20'"
                            class="px-4 py-2 rounded-full text-xs font-bold transition">
                        🏨 Directory ({{ $directoryCount }})
                    </button>
                    <button type="button" @click="mode = 'routes'; categoryFilter = ''; if(filteredItems()[0]) selected = filteredItems()[0]"
                            :class="mode === 'routes' ? 'bg-white text-chaouen-900' : 'bg-white/10 text-white hover:bg-white/20'"
                            class="px-4 py-2 rounded-full text-xs font-bold transition">
                        🥾 Routes ({{ $routesCount }})
                    </button>
                </div>
            </div>

            <!-- Search & Category Sub-filters -->
            <div class="mt-5 flex flex-col md:flex-row gap-3">
                <input type="text" x-model="search" @input="if(filteredItems()[0]) selected = filteredItems()[0]"
                       placeholder="Search places, hotels, restaurants, Akchour, Kasbah, parking..."
                       class="flex-1 px-4 py-2.5 rounded-xl bg-white text-slate-900 text-sm outline-none">
                <select x-model="categoryFilter" @change="mode = 'all'; if(filteredItems()[0]) selected = filteredItems()[0]"
                        class="md:w-64 px-4 py-2.5 rounded-xl bg-white text-slate-900 text-sm outline-none">
                    <option value="">All Categories & Routes</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->name }} ({{ $cat->places_count }})</option>
                    @endforeach
                    <option value="routes">🥾 Tourist Routes ({{ $routesCount }})</option>
                </select>
            </div>
        </div>

        <!-- Map Split Panel -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-white text-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-200 grid grid-cols-1 lg:grid-cols-12 min-h-[620px]">
                <!-- Left Scrollable Results List -->
                <div class="lg:col-span-5 border-r border-slate-200 max-h-[650px] overflow-y-auto divide-y divide-slate-100">
                    <template x-for="item in filteredItems()" :key="item.uid">
                        <div @click="selectItem(item)"
                             :class="selected && selected.uid === item.uid ? 'bg-chaouen-50 border-l-4 border-l-chaouen-600' : 'hover:bg-slate-50'"
                             class="p-4 cursor-pointer transition flex gap-3.5 items-start">
                            <img :src="item.image" :alt="item.title" class="w-20 h-20 rounded-xl object-cover shrink-0 border border-slate-200">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap gap-1 mb-1">
                                    <span class="px-2 py-0.5 rounded-full bg-chaouen-100 text-chaouen-800 text-[10px] font-bold" x-text="item.badge1"></span>
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-semibold" x-text="item.badge2"></span>
                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 text-[10px] font-semibold" x-text="item.badge3"></span>
                                </div>
                                <h3 class="font-bold text-sm text-slate-900 truncate" x-text="item.title"></h3>
                                <p class="text-xs text-slate-500 truncate" x-text="item.subtitle"></p>
                                <p class="text-xs text-slate-600 mt-1 line-clamp-2" x-text="item.description"></p>
                            </div>
                        </div>
                    </template>

                    <div x-show="filteredItems().length === 0" class="p-10 text-center text-sm text-slate-500">
                        No directory entries matched "<span x-text="search" class="font-semibold"></span>". Showing live Google Map search result on the right.
                    </div>
                </div>

                <!-- Right Live Google Map Embed + Selected Card Bar -->
                <div class="lg:col-span-7 flex flex-col bg-slate-100">
                    <div x-show="selected" class="bg-white border-b border-slate-200 p-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-chaouen-700 uppercase tracking-wider" x-text="selected?.category"></span>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs text-slate-600" x-text="selected?.subtitle"></span>
                            </div>
                            <h2 class="font-bold text-lg text-slate-900 truncate" x-text="selected?.title"></h2>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a :href="selected?.url" class="px-4 py-2 rounded-full bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-semibold transition">
                                View Full Details →
                            </a>
                            <a :href="selected?.external_map_url" target="_blank" rel="noopener" class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition">
                                Google Maps ↗
                            </a>
                        </div>
                    </div>
                    <div class="flex-1 min-h-[480px]">
                        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                                :src="getIframeSrc()"
                                title="Chefchaouen Interactive Google Map"
                                class="w-full h-full min-h-[520px]"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rich SEO, GEO & AISEO Geographic Orientation Guide -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-12">
        <!-- Quick Medina & Rif Orientation Grid -->
        <div>
            <div class="max-w-3xl mb-8">
                <span class="text-xs font-bold uppercase tracking-widest text-chaouen-600">Orientation & Navigation Guide (35.1688° N, -5.2636° W)</span>
                <h2 class="font-editorial text-3xl sm:text-4xl font-bold text-slate-900 mt-1.5">
                    Understanding Chefchaouen's Layout: Gates, Quarters & Mountain Trails
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-2 leading-relaxed">
                    Nestled around 600 meters up between the twin Rif peaks of Jebel Tissouka and Jebel El-Kelaa in northern Morocco (MA-01 region), Chefchaouen radiates naturally around its 15th-century car-free medina, western gates, eastern spring waters, and surrounding Talassemtane valleys.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-2">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-chaouen-50 text-chaouen-800 text-[11px] font-bold">1 · Central Heart</span>
                    <h3 class="font-bold text-base text-slate-900">Place Outa el-Hammam & Kasbah</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Reached by six historic alleys, this tree-shaded main plaza connects directly to the red-walled 1471 Kasbah Museum and the Grand Mosque's octagonal minaret.
                    </p>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('map-focus-query', { detail: 'Kasbah' }))"
                            class="text-xs font-bold text-chaouen-600 hover:underline pt-1">Locate on map ↑</button>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-2">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-sky-50 text-sky-800 text-[11px] font-bold">2 · Blue Photo Alleys</span>
                    <h3 class="font-bold text-base text-slate-900">Derb El-Asri, Souika & El Haouta</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        The medina's signature lime-washed stairways branch upward through El Haouta's zellige fountain and Souika, Chefchaouen's earliest Andalusian quarter.
                    </p>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('map-focus-query', { detail: 'Medina' }))"
                            class="text-xs font-bold text-chaouen-600 hover:underline pt-1">Locate on map ↑</button>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-2">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 text-[11px] font-bold">3 · Eastern Spring & Sunset</span>
                    <h3 class="font-bold text-base text-slate-900">Ras El Maa, Sebbanine & Spanish Mosque</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Exit the eastern medina walls at Ras El Maa mountain spring and cross the historic Portuguese Bridge to hike 25 minutes up to Bouzaafar's Spanish Mosque viewpoint.
                    </p>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('map-focus-query', { detail: 'Spanish Mosque' }))"
                            class="text-xs font-bold text-chaouen-600 hover:underline pt-1">Locate on map ↑</button>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-2">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-bold">4 · Parking, Taxis & Akchour</span>
                    <h3 class="font-bold text-base text-slate-900">Medina Gates & Outside City Trails</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Park outside the walls near Place El Makhzen or Bab El Ain, or take a shared grand taxi (20 MAD each way, ~45 min) out into Akchour Waterfalls & Talassemtane Park.
                    </p>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('map-focus-query', { detail: 'Akchour' }))"
                            class="text-xs font-bold text-chaouen-600 hover:underline pt-1">Locate on map ↑</button>
                </div>
            </div>
        </div>

        <!-- Neighborhood & Travel Logistics Table / Guide -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <article class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 p-7 sm:p-8 shadow-sm prose max-w-none">
                <h2>Chefchaouen Medina Neighborhoods, Parking & Transit Map Guide</h2>
                <p>
                    Chefchaouen is compact and pedestrian-friendly inside the historic walls, while dramatic Rif trails extend outward across the province. Using this live map alongside our curated <a href="{{ route('routes.index') }}">Tourist Routes</a> and <a href="{{ route('places.index') }}">Places Directory</a> helps travelers orient effortlessly from sunrise walks to mountain hikes.
                </p>

                <h3>Key Medina Zones & Historic Landmarks</h3>
                <ul>
                    <li><strong>Place Outa el-Hammam & 1471 Kasbah:</strong> Central cobblestone plaza anchored by Moussa Ben Ali's fortress gardens, Ethnographic Museum, and outdoor cafés.</li>
                    <li><strong>Souika & Rif El-Andalus:</strong> The earliest residential quarters settled in the late 15th century, filled with studded cedar doors, communal bread ovens, and weaving workshops.</li>
                    <li><strong>Place El Haouta & Derb El-Asri:</strong> Quiet cobalt alleyways famous for early-morning photography (best visited between 7:00 AM and 9:00 AM) and an octagonal zellige fountain.</li>
                    <li><strong>Ras El Maa & Sebbanine Quarter:</strong> Fresh mountain spring cascading beside riverside washbasins, Granada-inspired grain water mills, and the eastern trail gate.</li>
                    <li><strong>Place El Makhzen & Bab El Ain:</strong> Main perimeter parking and vehicle drop-off zones just steps from medina hotels and riads.</li>
                </ul>

                <h3>Regional Road & Hiking Distances from Chefchaouen</h3>
                <ul>
                    <li><strong>Bouzaafar Spanish Mosque Viewpoint:</strong> 1.5 km uphill walk (20 to 30 minutes) from Ras El Maa Gate.</li>
                    <li><strong>Akchour Waterfalls & God's Bridge:</strong> ~45 minutes northeast by shared grand taxi (20 MAD per seat) or private round trip (300 to 500 MAD).</li>
                    <li><strong>Azilan, Ain Tissamlal & Talassemtane National Park:</strong> 3.5 km to Panoramic Viewpoint (890m) and 16 km to Azilan cedar forests (1,270m).</li>
                    <li><strong>Major Cities:</strong> Tetouan (~1h 15m), Tangier (~2h 30m), Fez (~4h) via CTM bus or grand taxi.</li>
                </ul>
            </article>

            <!-- Map FAQs -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 p-7 shadow-sm space-y-5" x-data="{ openMapFaq: 0 }">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-chaouen-600">Navigation FAQs</span>
                    <h2 class="font-editorial text-2xl font-bold text-slate-900 mt-1">Common Chefchaouen Map Questions</h2>
                </div>

                @php
                    $mapFaqs = [
                        ['q' => 'Can cars drive inside Chefchaouen Old Medina?', 'a' => 'No. The historic Old Medina is completely car-free and explored on foot. Drivers should park outside the walls at Place El Makhzen (near Hotel Parador), Bab El Ain, or Ras El Maa and walk 2 to 10 minutes into town.'],
                        ['q' => 'Where is the main central square on the map?', 'a' => 'Place Outa el-Hammam sits right beside the 1471 Kasbah fortress and the Grand Mosque minaret, connected by six walkable alleys.'],
                        ['q' => 'How far is Akchour Waterfalls from Chefchaouen?', 'a' => 'Akchour and God’s Bridge lie about 45 minutes northeast in Talassemtane National Park. Shared grand taxis depart for ~20 MAD per person each way; aim to return before sunset.'],
                        ['q' => 'How long does it take to walk to the Spanish Mosque?', 'a' => 'Walking uphill from Ras El Maa Gate to the Bouzaafar Spanish Mosque takes roughly 20 to 30 minutes for golden sunset panoramic views.'],
                    ];
                @endphp

                <div class="space-y-2.5">
                    @foreach($mapFaqs as $idx => $mf)
                        <div class="rounded-2xl border border-slate-200 overflow-hidden">
                            <button type="button" @click="openMapFaq = openMapFaq === {{ $idx }} ? null : {{ $idx }}"
                                    class="w-full px-4 py-3.5 text-left flex items-center justify-between gap-3 text-xs sm:text-sm font-bold text-slate-900 bg-slate-50/70 hover:bg-chaouen-50/50 transition">
                                <span>{{ $mf['q'] }}</span>
                                <span class="text-chaouen-600 text-base shrink-0" x-text="openMapFaq === {{ $idx }} ? '−' : '+'"></span>
                            </button>
                            <div x-show="openMapFaq === {{ $idx }}" x-cloak class="px-4 py-3 text-xs text-slate-600 leading-relaxed bg-white border-t border-slate-100">
                                {{ $mf['a'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2 flex flex-wrap gap-2">
                    <a href="{{ route('page.show', 'visit-plans') }}" class="px-4 py-2 rounded-full bg-chaouen-600 text-white text-xs font-bold hover:bg-chaouen-700 transition">
                        View 1-to-3 Day Visit Plans →
                    </a>
                    <button type="button" @click="$dispatch('open-chaouen-chat', { prompt: 'Help me navigate Chefchaouen parking gates and walking routes' })"
                            class="px-4 py-2 rounded-full bg-chaouen-50 text-chaouen-800 border border-chaouen-200 text-xs font-bold hover:bg-chaouen-100 transition">
                        ✨ Ask AI Map Assistant
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection
