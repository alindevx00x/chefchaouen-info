@extends('layouts.admin')

@section('header', 'Platform Overview')

@section('content')
    <!-- KPI Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <span class="text-xs font-semibold text-slate-500">Published Places</span>
            <p class="text-3xl font-bold text-chaouen-700 mt-1">{{ $stats['places_published'] }}</p>
            <span class="text-[11px] text-amber-600 font-medium">{{ $stats['places_drafts'] }} drafts / suggestions</span>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <span class="text-xs font-semibold text-slate-500">Tourist Routes</span>
            <p class="text-3xl font-bold text-emerald-700 mt-1">{{ $stats['routes_published'] }}</p>
            <span class="text-[11px] text-amber-600 font-medium">{{ $stats['routes_drafts'] }} route drafts</span>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <span class="text-xs font-semibold text-slate-500">Pending Reviews</span>
            <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['reviews_pending'] }}</p>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="text-[11px] text-chaouen-600 hover:underline">Moderate reviews →</a>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <span class="text-xs font-semibold text-slate-500">AI Guide Active Model</span>
            <p class="text-sm font-bold text-slate-900 mt-2 truncate">{{ $defaultAi?->name ?? 'Local Engine' }}</p>
            <p class="text-xs text-chaouen-600 truncate">{{ $defaultAi?->selected_model ?? 'database-rag' }}</p>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
            <span class="text-xl font-bold text-slate-900">{{ $stats['public_pages'] }}</span>
            <p class="text-xs text-slate-500">Public Pages</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
            <span class="text-xl font-bold text-slate-900">{{ $stats['gallery_photos'] }}</span>
            <p class="text-xs text-slate-500">Gallery Photos</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
            <span class="text-xl font-bold text-slate-900">{{ $stats['blog_posts'] }}</span>
            <p class="text-xs text-slate-500">Blog Stories</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
            <span class="text-xl font-bold text-slate-900">{{ $stats['newsletters'] }}</span>
            <p class="text-xs text-slate-500">Newsletter Subscribers</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
            <span class="text-xl font-bold text-slate-900">{{ $stats['chat_queries'] }}</span>
            <p class="text-xs text-slate-500">Chatbot Queries</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Community Suggested Drafts -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-base text-slate-900">Community Suggestions (Drafts)</h2>
                <a href="{{ route('admin.places.index', ['status' => 'draft']) }}" class="text-xs font-semibold text-chaouen-600 hover:underline">View All Drafts →</a>
            </div>
            @if($suggestedPlaces->isEmpty() && $suggestedRoutes->isEmpty())
                <p class="text-xs text-slate-500 py-4">No visitor-suggested drafts pending right now.</p>
            @else
                <div class="space-y-3">
                    @foreach($suggestedPlaces as $sp)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">{{ $sp->status }}</span>
                                <p class="font-bold text-sm text-slate-900 mt-1">{{ $sp->title }}</p>
                                <p class="text-xs text-slate-500">Suggested by {{ $sp->suggested_by_name }} ({{ $sp->suggested_by_email }})</p>
                            </div>
                            <a href="{{ route('admin.places.edit', $sp) }}" class="px-3 py-1.5 rounded-lg bg-chaouen-600 text-white text-xs font-semibold">Review</a>
                        </div>
                    @endforeach
                    @foreach($suggestedRoutes as $sr)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Route {{ $sr->status }}</span>
                                <p class="font-bold text-sm text-slate-900 mt-1">{{ $sr->title }}</p>
                                <p class="text-xs text-slate-500">Suggested by {{ $sr->suggested_by_name }}</p>
                            </div>
                            <a href="{{ route('admin.routes.edit', $sr) }}" class="px-3 py-1.5 rounded-lg bg-chaouen-600 text-white text-xs font-semibold">Review</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Pending Reviews -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-base text-slate-900">Pending Reviews & Ratings</h2>
                <a href="{{ route('admin.reviews.index') }}" class="text-xs font-semibold text-chaouen-600 hover:underline">Manage All →</a>
            </div>
            @if($pendingReviews->isEmpty())
                <p class="text-xs text-slate-500 py-4">All visitor reviews are moderated.</p>
            @else
                <div class="space-y-3">
                    @foreach($pendingReviews as $rev)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-bold text-sm text-slate-900 truncate">{{ $rev->visitor_name }} • {{ str_repeat('★', $rev->rating) }}</p>
                                <p class="text-xs text-slate-600 truncate">{{ $rev->comment }}</p>
                            </div>
                            <form action="{{ route('admin.reviews.approve', $rev) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold">Approve</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
