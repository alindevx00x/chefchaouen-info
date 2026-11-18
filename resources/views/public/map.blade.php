@extends('layouts.public')

@section('title', 'Interactive Google Map of Chefchaouen | Places & Routes')

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
                }
             }">
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
                                class="w-full h-full min-h-[520px]"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
