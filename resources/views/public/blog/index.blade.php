@extends('layouts.public')

@section('title', 'Chefchaouen Journal & Travel Stories | Chefchaouen.info')
@section('meta_description', 'Read travel stories, cultural history guides from 1471, Rif trekking notes, and insider tips for exploring Chefchaouen, Morocco.')
@section('meta_keywords', 'Chefchaouen travel blog, Chefchaouen history 1471, Rif hiking notes, Blue Pearl stories')

@push('schema')
    @php
        $blogIndexSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Journal', 'item' => route('blog.index')],
                    ],
                ],
                [
                    '@type' => 'Blog',
                    'name' => 'Chefchaouen Journal & Travel Stories',
                    'url' => route('blog.index'),
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($blogIndexSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="bg-chaouen-900 text-white py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold uppercase tracking-widest text-sky-300">Blue Pearl Journal</span>
            <h1 class="font-editorial text-4xl sm:text-5xl font-bold mt-2 mb-3">Stories, Trails & Local Insights</h1>
            <p class="text-chaouen-100 max-w-2xl text-sm sm:text-base">In-depth travel notes, Rif hiking planners, and cultural dispatches from Chefchaouen.</p>
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-full text-xs font-bold {{ !request('category') ? 'bg-white text-chaouen-900' : 'bg-white/15 text-white' }}">All Articles</a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.index', ['category' => $cat->slug]) }}" class="px-4 py-2 rounded-full text-xs font-bold {{ request('category') === $cat->slug ? 'bg-white text-chaouen-900' : 'bg-white/15 text-white' }}">{{ $cat->name }} ({{ $cat->posts_count }})</a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($posts as $post)
                <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl transition flex flex-col">
                    <a href="{{ route('blog.show', $post->slug) }}" class="aspect-[4/3] overflow-hidden bg-slate-100 block">
                        <img src="{{ asset($post->featured_image ?: '/images/gallery/blue-stairs.jpg') }}" alt="{{ $post->title }}" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    </a>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 mb-2">
                                <span class="font-bold text-chaouen-700">{{ $post->category->name ?? 'Travel' }}</span>
                                <span>{{ $post->reading_time_minutes }} min read</span>
                            </div>
                            <h2 class="font-bold text-base text-slate-900 leading-snug mb-2">
                                <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-chaouen-600 transition">{{ $post->title }}</a>
                            </h2>
                            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">{{ $post->excerpt }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-400">{{ $post->published_at?->format('M d, Y') }}</span>
                            <a href="{{ route('blog.show', $post->slug) }}" class="font-semibold text-chaouen-600 hover:underline">Read story →</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">{{ $posts->links() }}</div>
    </section>
@endsection
