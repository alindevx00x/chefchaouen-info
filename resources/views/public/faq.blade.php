@extends('layouts.public')

@section('title', 'Frequently Asked Questions | Chefchaouen.info')
@section('meta_description', 'Answers to the most common travel questions about visiting Chefchaouen, hiking Akchour Waterfalls, parking near the medina, local taxis, and safety in Morocco.')
@section('meta_keywords', 'Chefchaouen FAQ, Akchour taxi price, Chefchaouen parking, visiting Chefchaouen Morocco tips')

@push('schema')
    @php
        $faqEntities = $faqs->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq->question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq->answer,
            ],
        ])->values()->all();
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'FAQs', 'item' => route('faq.index')],
                    ],
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => $faqEntities,
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="bg-chaouen-900 text-white py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold uppercase tracking-widest text-sky-300">Traveler Answers</span>
            <h1 class="font-editorial text-4xl sm:text-5xl font-bold mt-2">Frequently Asked Questions</h1>
            <p class="text-chaouen-100 mt-2 text-sm sm:text-base">Practical guidance for visiting Chefchaouen, hiking Akchour, parking, and exploring the medina.</p>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ openIdx: 0 }">
        <div class="space-y-3">
            @foreach($faqs as $idx => $faq)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <button type="button" @click="openIdx = openIdx === {{ $idx }} ? null : {{ $idx }}"
                            class="w-full px-6 py-4 text-left flex items-center justify-between gap-4">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-chaouen-600 block">{{ $faq->category }}</span>
                            <span class="font-bold text-base text-slate-900">{{ $faq->question }}</span>
                        </div>
                        <span class="text-chaouen-600 text-xl font-bold" x-text="openIdx === {{ $idx }} ? '−' : '+'"></span>
                    </button>
                    <div x-show="openIdx === {{ $idx }}" x-cloak class="px-6 pb-5 pt-2 text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                        {{ $faq->answer }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
