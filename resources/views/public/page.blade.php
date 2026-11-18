@extends('layouts.public')

@section('title', ($page->meta_title ?: $page->title) . ' | Chefchaouen.info')
@section('meta_description', $page->meta_description ?: $page->subtitle)

@section('content')
    <section class="bg-gradient-to-b from-chaouen-900 to-chaouen-800 text-white py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($page->hero_badge)
                <span class="inline-block px-3 py-1 rounded-full bg-white/15 text-sky-200 text-xs font-bold uppercase tracking-wider mb-3">{{ $page->hero_badge }}</span>
            @endif
            <h1 class="font-editorial text-4xl sm:text-5xl font-bold">{{ $page->title }}</h1>
            @if($page->subtitle)
                <p class="text-chaouen-100 mt-3 text-base">{{ $page->subtitle }}</p>
            @endif
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        @if($page->featured_image)
            <div class="aspect-[16/9] rounded-3xl overflow-hidden border border-slate-200 shadow-sm">
                <img src="{{ asset($page->featured_image) }}" alt="{{ $page->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-200 p-7 sm:p-10 prose prose-slate max-w-none leading-relaxed">
            {!! $page->content !!}
        </div>

        <!-- Attached Downloadable PDF Files -->
        @if(is_array($page->pdf_attachments) && count($page->pdf_attachments) > 0)
            <div class="bg-chaouen-50 border border-chaouen-200 rounded-3xl p-6 sm:p-8">
                <h2 class="font-editorial text-2xl font-bold text-slate-900 mb-2">Downloadable Guides & PDF Documents</h2>
                <p class="text-xs text-slate-600 mb-5">Save these offline resources for your walk through Chefchaouen.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($page->pdf_attachments as $pdf)
                        <a href="{{ asset($pdf['file_path']) }}" target="_blank" download
                           class="bg-white rounded-2xl border border-chaouen-200 p-4 flex items-center justify-between gap-4 hover:shadow-md transition group">
                            <div class="min-w-0">
                                <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full mb-1">PDF {{ $pdf['file_size'] ?? '' }}</span>
                                <h3 class="font-bold text-sm text-slate-900 group-hover:text-chaouen-700 truncate">{{ $pdf['title'] ?? 'Download PDF' }}</h3>
                                @if(!empty($pdf['description']))
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $pdf['description'] }}</p>
                                @endif
                            </div>
                            <span class="px-3.5 py-2 rounded-xl bg-chaouen-600 text-white text-xs font-bold shrink-0">Download ↓</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
