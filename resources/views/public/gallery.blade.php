@extends('layouts.public')

@section('title', 'Chefchaouen Photo Gallery | Chefchaouen.info')
@section('meta_description', 'Browse high-resolution photography of Chefchaouen cobalt alleys, Andalusian doors, Ras El Maa waterfall, Akchour cascades, and the Rif peaks.')
@section('meta_keywords', 'Chefchaouen photography, Blue Pearl gallery, Derb El Asri photos, Akchour waterfalls images')

@push('schema')
    @php
        $gallerySchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Photo Gallery', 'item' => route('gallery.index')],
                    ],
                ],
                [
                    '@type' => 'ImageGallery',
                    'name' => 'Chefchaouen Photo Gallery',
                    'url' => route('gallery.index'),
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($gallerySchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="bg-chaouen-900 text-white py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold uppercase tracking-widest text-sky-300">Follow the journey</span>
            <h1 class="font-editorial text-4xl sm:text-5xl font-bold mt-2 mb-3">Chefchaouen Photo Gallery</h1>
            <p class="text-chaouen-100 text-sm sm:text-base max-w-2xl">Explore the cobalt lanes, Akchour cascades, Andalusian doors, and Rif mountain horizons.</p>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('gallery.index') }}" class="px-4 py-2 rounded-full text-xs font-bold {{ !request('category') ? 'bg-white text-chaouen-900' : 'bg-white/15 text-white hover:bg-white/25' }}">All Photos</a>
                @foreach($categories as $cat)
                    <a href="{{ route('gallery.index', ['category' => $cat]) }}" class="px-4 py-2 rounded-full text-xs font-bold {{ request('category') === $cat ? 'bg-white text-chaouen-900' : 'bg-white/15 text-white hover:bg-white/25' }}">{{ $cat }}</a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ lightbox: null }">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($photos as $photo)
                <div @click="lightbox = { src: '{{ asset($photo->image_path) }}', title: @js($photo->title), caption: @js($photo->caption), location: @js($photo->location_tag) }"
                     class="group cursor-pointer bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl transition">
                    <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                        <img src="{{ asset($photo->image_path) }}" alt="{{ $photo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-4">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-chaouen-600">{{ $photo->category }}</span>
                        <h3 class="font-bold text-sm text-slate-900 mt-0.5">{{ $photo->title }}</h3>
                        @if($photo->location_tag)
                            <p class="text-xs text-slate-500 mt-1">📍 {{ $photo->location_tag }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Lightbox Modal -->
        <div x-show="lightbox" x-cloak @click.self="lightbox = null" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-3xl w-full overflow-hidden shadow-2xl">
                <img :src="lightbox?.src" :alt="lightbox?.title" class="w-full max-h-[70vh] object-cover">
                <div class="p-5 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900" x-text="lightbox?.title"></h3>
                        <p class="text-xs text-slate-600 mt-0.5" x-text="lightbox?.caption || lightbox?.location"></p>
                    </div>
                    <button type="button" @click="lightbox = null" class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold">Close</button>
                </div>
            </div>
        </div>
    </section>
@endsection
