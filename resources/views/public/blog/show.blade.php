@extends('layouts.public')

@section('title', ($post->meta_title ?: $post->title) . ' | Chefchaouen.info')
@section('meta_description', $post->meta_description ?: $post->excerpt)
@section('og_type', 'article')
@if($post->featured_image)
    @section('og_image', asset($post->featured_image))
@endif

@push('schema')
    @php
        $blogSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Journal', 'item' => route('blog.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => route('blog.show', $post->slug)],
                    ],
                ],
                [
                    '@type' => 'BlogPosting',
                    'headline' => $post->title,
                    'description' => $post->meta_description ?: $post->excerpt,
                    'url' => route('blog.show', $post->slug),
                    'image' => asset($post->featured_image ?: '/images/gallery/hero-medina.jpg'),
                    'datePublished' => optional($post->published_at ?? $post->created_at)->toAtomString(),
                    'dateModified' => optional($post->updated_at)->toAtomString(),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $post->author?->name ?: 'Chefchaouen.info Editorial',
                    ],
                    'publisher' => ['@id' => url('/') . '#organization'],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($blogSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-xs text-slate-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-chaouen-600">Home</a> /
            <a href="{{ route('blog.index') }}" class="hover:text-chaouen-600">Journal</a> /
            <span class="text-slate-900">{{ $post->title }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            @if($post->category)
                <span class="px-3 py-1 rounded-full bg-chaouen-100 text-chaouen-800 text-xs font-bold">{{ $post->category->name }}</span>
            @endif
            <span class="text-xs text-slate-500">{{ $post->published_at?->format('F d, Y') }} • {{ $post->reading_time_minutes }} min read</span>
        </div>

        <h1 class="font-editorial text-4xl sm:text-5xl font-bold text-slate-900 leading-tight mb-6">{{ $post->title }}</h1>

        @if($post->featured_image)
            <div class="mb-8">
                @include('public.partials.media-gallery', [
                    'featured' => $post->featured_image,
                    'album' => [],
                    'title' => $post->title,
                ])
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-200 p-7 sm:p-10 prose prose-slate max-w-none leading-relaxed">
            {!! $post->content !!}
        </div>
    </article>

    @if($relatedPosts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 border-t border-slate-200">
            <h2 class="font-editorial text-3xl font-bold text-slate-900 mb-6">More from the Journal</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedPosts as $rp)
                    <a href="{{ route('blog.show', $rp->slug) }}" class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition block">
                        <img src="{{ asset($rp->featured_image ?: '/images/gallery/hero-medina.jpg') }}" alt="{{ $rp->title }}" class="aspect-[4/3] w-full object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-sm text-slate-900 line-clamp-2">{{ $rp->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
