@extends('layouts.admin')

@section('header', $route->exists ? 'Edit Route: ' . $route->title : 'Add Tourist Route')

@section('content')
    <form action="{{ $route->exists ? route('admin.routes.update', $route) : route('admin.routes.store') }}"
          method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
        @csrf
        @if($route->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Route Name (English) *</label>
                <input type="text" name="title" value="{{ old('title', $route->title) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Arabic Name (إسم المسار)</label>
                <input type="text" name="title_ar" value="{{ old('title_ar', $route->title_ar) }}" placeholder="مسار أقشور" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Route Type (نوع المسار) *</label>
                <select name="route_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm">
                    <option value="inside_city" @selected(old('route_type', $route->route_type) === 'inside_city')>Inside City (داخل المدينة)</option>
                    <option value="outside_city" @selected(old('route_type', $route->route_type) === 'outside_city')>Outside City / Province (خارج المدينة)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Difficulty (صعوبة المسار) *</label>
                <select name="difficulty" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm">
                    @foreach(['Easy', 'Moderate', 'Challenging'] as $d)
                        <option value="{{ $d }}" @selected(old('difficulty', $route->difficulty) === $d)>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Estimated Walking Time</label>
                <input type="text" name="estimated_walking_time" value="{{ old('estimated_walking_time', $route->estimated_walking_time) }}" placeholder="e.g. 2 - 3 hours" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Distance</label>
                <input type="text" name="distance_km" value="{{ old('distance_km', $route->distance_km) }}" placeholder="e.g. 4.5 km" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm">
                    <option value="published" @selected(old('status', $route->status) === 'published')>Published</option>
                    <option value="draft" @selected(old('status', $route->status) === 'draft')>Draft</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Map From (Start Point)</label>
                <input type="text" name="start_point" value="{{ old('start_point', $route->start_point) }}" placeholder="Ras El Maa Gate" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Map To (End Point)</label>
                <input type="text" name="end_point" value="{{ old('end_point', $route->end_point) }}" placeholder="Spanish Mosque" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Google Map Query (From → To)</label>
                <input type="text" name="map_query" value="{{ old('map_query', $route->map_query) }}" placeholder="Ras El Maa to Spanish Mosque Chefchaouen" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Google Map Iframe Override (Optional)</label>
            <textarea name="map_iframe" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono">{{ old('map_iframe', $route->map_iframe) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Short Summary</label>
            <textarea name="short_description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('short_description', $route->short_description) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Detailed Route Information (معلومات مفصلة للمسار - TinyMCE Self-Hosted CDN)</label>
            <textarea name="detailed_info" class="tinymce-editor">{{ old('detailed_info', $route->detailed_info) }}</textarea>
        </div>

        <!-- Featured & Reorderable Route Gallery Album -->
        @include('admin.partials.album-manager', [
            'featured' => $route->featured_image,
            'album' => $route->album_photos,
            'fallback' => '/images/gallery/rif-mountains.jpg',
        ])

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Waypoints (Comma-separated)</label>
                <input type="text" name="waypoints_text" value="{{ old('waypoints_text', is_array($route->waypoints) ? implode(', ', $route->waypoints) : '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">What to Bring (Comma-separated)</label>
                <input type="text" name="what_to_bring_text" value="{{ old('what_to_bring_text', is_array($route->what_to_bring) ? implode(', ', $route->what_to_bring) : '') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Ask AI Prompt Question</label>
                <input type="text" name="ai_prompt_question" value="{{ old('ai_prompt_question', $route->ai_prompt_question) }}" placeholder="How do I hike this?" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>

        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $route->is_featured))> ★ Show in Featured Suggested Routes
        </label>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.routes.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">Save Tourist Route</button>
        </div>
    </form>
@endsection
