@extends('layouts.admin')

@section('header', 'Platform Overview & Analytics')

@section('content')
    <!-- Primary KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Published Places</span>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-chaouen-50 text-chaouen-700">{{ $stats['categories_count'] }} Categories</span>
            </div>
            <p class="text-3xl font-bold text-chaouen-700 mt-1">{{ $stats['places_published'] }}</p>
            <div class="mt-2 flex flex-wrap gap-2 text-[11px]">
                <span class="text-emerald-700 font-semibold">✓ {{ $stats['places_verified'] }} verified</span>
                <span class="text-slate-300">|</span>
                <span class="text-amber-600 font-semibold">✨ {{ $stats['places_hidden_gems'] }} gems</span>
                <span class="text-slate-300">|</span>
                <span class="text-slate-500">{{ $stats['places_drafts'] }} drafts</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Tourist Routes</span>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Inside &amp; Outside</span>
            </div>
            <p class="text-3xl font-bold text-emerald-700 mt-1">{{ $stats['routes_published'] }}</p>
            <div class="mt-2 flex flex-wrap gap-2 text-[11px]">
                <span class="text-chaouen-700 font-semibold">{{ $stats['routes_inside'] }} inside city</span>
                <span class="text-slate-300">|</span>
                <span class="text-emerald-700 font-semibold">{{ $stats['routes_outside'] }} outside city</span>
                <span class="text-slate-300">|</span>
                <span class="text-slate-500">{{ $stats['routes_drafts'] }} drafts</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Reviews &amp; Directory Score</span>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">★ {{ number_format($stats['avg_place_rating'], 2) }} avg</span>
            </div>
            <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['reviews_approved'] + $stats['reviews_pending'] }}</p>
            <div class="mt-2 flex items-center justify-between text-[11px]">
                <span class="text-emerald-700 font-semibold">{{ $stats['reviews_approved'] }} approved · {{ $stats['reviews_pending'] }} pending</span>
                <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="text-chaouen-600 font-semibold hover:underline">Moderate →</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">AI Guide &amp; Queries</span>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-sky-50 text-sky-700">{{ $activeAiCount }} providers</span>
            </div>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['chat_queries'] }}</p>
            <div class="mt-2 flex items-center justify-between gap-2 text-[11px]">
                <span class="text-chaouen-700 font-semibold truncate">{{ $defaultAi?->name ?? 'Local RAG' }} ({{ $defaultAi?->selected_model ?? 'database-rag' }})</span>
                <span class="text-rose-600 font-medium shrink-0">{{ $stats['chat_blocked'] }} blocked</span>
            </div>
        </div>
    </div>

    <!-- Secondary Stats Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
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
            <span class="text-xl font-bold text-slate-900">{{ $stats['faqs_count'] }}</span>
            <p class="text-xs text-slate-500">Active FAQs</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
            <span class="text-xl font-bold text-chaouen-700">{{ $stats['newsletters'] }}</span>
            <p class="text-xs text-slate-500">Subscribers</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 text-center">
            <span class="text-xl font-bold text-rose-600">{{ $stats['contacts_new'] }}</span>
            <p class="text-xs text-slate-500">New Inquiries ({{ $stats['contacts_total'] }})</p>
        </div>
    </div>

    <!-- Charts Row 1: Category Distribution + Neighborhood Density -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-base text-slate-900">Places Directory by Category</h2>
                    <p class="text-xs text-slate-500">Published listings across all 9 Chefchaouen categories</p>
                </div>
                <a href="{{ route('admin.places.index') }}" class="text-xs font-semibold text-chaouen-600 hover:underline">Manage Places →</a>
            </div>
            <div class="h-72">
                <canvas id="placesCategoryChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-base text-slate-900">Neighborhood Distribution</h2>
                    <p class="text-xs text-slate-500">Top areas across Chefchaouen Medina &amp; Rif Province</p>
                </div>
                <span class="text-[11px] font-semibold text-slate-400">Top 8 areas</span>
            </div>
            <div class="h-72">
                <canvas id="neighborhoodChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: 7-Day Activity Trend + Directory & Routes Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-base text-slate-900">7-Day Engagement &amp; Community Activity</h2>
                    <p class="text-xs text-slate-500">Daily AI Guide queries, reviews, contact submissions, and newsletter signups</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-chaouen-50 text-chaouen-700">Last 7 Days</span>
            </div>
            <div class="h-64">
                <canvas id="activityTrendChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="font-bold text-base text-slate-900 mb-1">Routes &amp; Curation Mix</h2>
                <p class="text-xs text-slate-500 mb-4">Difficulty &amp; quality signals across the directory</p>
                <div class="h-44">
                    <canvas id="routesDifficultyChart"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2 pt-4 mt-4 border-t border-slate-100 text-center text-xs">
                <div class="p-2 rounded-xl bg-chaouen-50">
                    <span class="font-bold text-chaouen-700 block text-sm">{{ $stats['places_featured'] }}</span>
                    <span class="text-[11px] text-slate-600">Featured</span>
                </div>
                <div class="p-2 rounded-xl bg-amber-50">
                    <span class="font-bold text-amber-700 block text-sm">{{ $stats['places_hidden_gems'] }}</span>
                    <span class="text-[11px] text-slate-600">Hidden Gems</span>
                </div>
                <div class="p-2 rounded-xl bg-emerald-50">
                    <span class="font-bold text-emerald-700 block text-sm">{{ $stats['places_verified'] }}</span>
                    <span class="text-[11px] text-slate-600">Verified</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Tables: Top Rated Places & Recent AI Chatbot Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-base text-slate-900">Top Rated Places in Directory</h2>
                <a href="{{ route('admin.places.index') }}" class="text-xs font-semibold text-chaouen-600 hover:underline">All {{ $stats['places_published'] }} Places →</a>
            </div>
            <div class="space-y-2.5">
                @foreach($topRatedPlaces as $tp)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset($tp->featured_image ?: '/images/gallery/medina-door.jpg') }}" alt="{{ $tp->title }}" class="w-11 h-11 rounded-xl object-cover shrink-0 border border-slate-200">
                            <div class="min-w-0">
                                <p class="font-bold text-sm text-slate-900 truncate">{{ $tp->title }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $tp->category?->name }} · {{ $tp->neighborhood }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold">★ {{ number_format($tp->rating_avg, 1) }}</span>
                            <a href="{{ route('admin.places.edit', $tp) }}" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-chaouen-50 text-xs font-semibold text-slate-700">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-base text-slate-900">Recent AI Guide Chatbot Queries</h2>
                <a href="{{ route('admin.settings.index', ['tab' => 'ai']) }}" class="text-xs font-semibold text-chaouen-600 hover:underline">AI Settings →</a>
            </div>
            @if($recentChatLogs->isEmpty())
                <p class="text-xs text-slate-500 py-4">No visitor questions logged with the Chefchaouen Guide AI yet.</p>
            @else
                <div class="space-y-2.5">
                    @foreach($recentChatLogs as $log)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $log->was_blocked_off_topic ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $log->was_blocked_off_topic ? 'Guardrail Blocked' : 'Answered' }}
                                </span>
                                <span class="text-[11px] text-slate-400">{{ $log->provider_used ?: 'AI' }} · {{ $log->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs font-semibold text-slate-900 line-clamp-1">{{ $log->user_message }}</p>
                            <p class="text-[11px] text-slate-500 line-clamp-1">{{ strip_tags($log->bot_response) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Community Suggestions, Pending Reviews & Recent Contact Inquiries -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-base text-slate-900">Community Drafts</h2>
                <a href="{{ route('admin.places.index', ['status' => 'draft']) }}" class="text-xs font-semibold text-chaouen-600 hover:underline">Drafts →</a>
            </div>
            @if($suggestedPlaces->isEmpty() && $suggestedRoutes->isEmpty())
                <p class="text-xs text-slate-500 py-4">No visitor-suggested drafts pending right now.</p>
            @else
                <div class="space-y-3">
                    @foreach($suggestedPlaces as $sp)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">{{ $sp->status }}</span>
                                <p class="font-bold text-sm text-slate-900 mt-1 truncate">{{ $sp->title }}</p>
                                <p class="text-xs text-slate-500 truncate">By {{ $sp->suggested_by_name }}</p>
                            </div>
                            <a href="{{ route('admin.places.edit', $sp) }}" class="px-3 py-1.5 rounded-lg bg-chaouen-600 text-white text-xs font-semibold shrink-0">Review</a>
                        </div>
                    @endforeach
                    @foreach($suggestedRoutes as $sr)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Route {{ $sr->status }}</span>
                                <p class="font-bold text-sm text-slate-900 mt-1 truncate">{{ $sr->title }}</p>
                                <p class="text-xs text-slate-500 truncate">By {{ $sr->suggested_by_name }}</p>
                            </div>
                            <a href="{{ route('admin.routes.edit', $sr) }}" class="px-3 py-1.5 rounded-lg bg-chaouen-600 text-white text-xs font-semibold shrink-0">Review</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-base text-slate-900">Pending Reviews</h2>
                <a href="{{ route('admin.reviews.index') }}" class="text-xs font-semibold text-chaouen-600 hover:underline">Manage All →</a>
            </div>
            @if($pendingReviews->isEmpty())
                <p class="text-xs text-slate-500 py-4">All visitor reviews are moderated.</p>
            @else
                <div class="space-y-3">
                    @foreach($pendingReviews as $rev)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-bold text-sm text-slate-900 truncate">{{ $rev->visitor_name }} · {{ str_repeat('★', $rev->rating) }}</p>
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

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-base text-slate-900">Recent Contact Messages</h2>
                <a href="{{ route('admin.contacts.index') }}" class="text-xs font-semibold text-chaouen-600 hover:underline">Inbox →</a>
            </div>
            @if($recentContacts->isEmpty())
                <p class="text-xs text-slate-500 py-4">No contact messages submitted yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentContacts as $msg)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-xs text-slate-900 truncate">{{ $msg->name }}</span>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $msg->status === 'new' ? 'bg-sky-100 text-sky-800' : 'bg-slate-200 text-slate-700' }}">{{ $msg->status }}</span>
                            </div>
                            <p class="text-xs text-slate-600 truncate">{{ $msg->subject ?: $msg->message }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const categoryLabels = @json($categoryStats->pluck('name'));
    const categoryCounts = @json($categoryStats->pluck('published_count'));

    new Chart(document.getElementById('placesCategoryChart'), {
        type: 'bar',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Published Places',
                data: categoryCounts,
                backgroundColor: ['#1d4ed8', '#0284c7', '#1e40af', '#0369a1', '#2563eb', '#1e3a8a', '#0f766e', '#475569', '#0ea5e9'],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            }
        }
    });

    const neighborhoodLabels = @json($neighborhoodStats->pluck('neighborhood'));
    const neighborhoodCounts = @json($neighborhoodStats->pluck('total'));

    new Chart(document.getElementById('neighborhoodChart'), {
        type: 'bar',
        data: {
            labels: neighborhoodLabels,
            datasets: [{
                label: 'Listings',
                data: neighborhoodCounts,
                backgroundColor: '#2563eb',
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
                y: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('activityTrendChart'), {
        type: 'line',
        data: {
            labels: @json($activityTrend['labels']),
            datasets: [
                { label: 'AI Chatbot', data: @json($activityTrend['chats']), borderColor: '#1d4ed8', backgroundColor: 'rgba(29, 78, 216, 0.12)', tension: 0.35, fill: true },
                { label: 'Reviews', data: @json($activityTrend['reviews']), borderColor: '#d97706', backgroundColor: 'rgba(217, 119, 6, 0.1)', tension: 0.35, fill: false },
                { label: 'Contacts', data: @json($activityTrend['contacts']), borderColor: '#059669', backgroundColor: 'rgba(5, 150, 105, 0.1)', tension: 0.35, fill: false },
                { label: 'Subscribers', data: @json($activityTrend['newsletters']), borderColor: '#0284c7', backgroundColor: 'rgba(2, 132, 199, 0.1)', tension: 0.35, fill: false }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('routesDifficultyChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($routeDifficultyStats)),
            datasets: [{
                data: @json(array_values($routeDifficultyStats)),
                backgroundColor: ['#059669', '#0284c7', '#d97706'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '64%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
        }
    });
});
</script>
@endpush
