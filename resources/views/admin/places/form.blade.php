@extends('layouts.admin')

@section('header', $place->exists ? 'Edit Place: ' . $place->title : 'Add New Place')

@section('content')
    <form action="{{ $place->exists ? route('admin.places.update', $place) : route('admin.places.store') }}"
          method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
        @csrf
        @if($place->exists) @method('PUT') @endif

        @if($place->suggested_by_name)
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                <strong>Community Suggestion Draft:</strong> Submitted by <strong>{{ $place->suggested_by_name }}</strong> ({{ $place->suggested_by_email }}). Notes: {{ $place->suggested_notes ?: 'None' }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Place Title *</label>
                <input type="text" name="title" value="{{ old('title', $place->title) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category *</label>
                <select name="place_category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm">
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('place_category_id', $place->place_category_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Subtitle</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $place->subtitle) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Price Range</label>
                <select name="price_range" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm">
                    @foreach(['Free', '$', '$$', '$$$'] as $pr)
                        <option value="{{ $pr }}" @selected(old('price_range', $place->price_range) === $pr)>{{ $pr }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm">
                    <option value="published" @selected(old('status', $place->status) === 'published')>Published</option>
                    <option value="draft" @selected(old('status', $place->status) === 'draft')>Draft</option>
                    <option value="pending" @selected(old('status', $place->status) === 'pending')>Pending Review</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Short Description</label>
            <textarea name="short_description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('short_description', $place->short_description) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Full Description (TinyMCE Rich Editor)</label>
            <textarea name="description" class="tinymce-editor">{{ old('description', $place->description) }}</textarea>
        </div>

        <!-- Images -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Featured Image (Upload)</label>
                <input type="file" name="featured_image_file" accept="image/*" class="w-full text-xs mb-2">
                <input type="text" name="featured_image_url" value="{{ old('featured_image_url', $place->featured_image) }}" placeholder="/images/gallery/medina-door.jpg" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Album Photos (Upload Multiple or 1 URL per line)</label>
                <input type="file" name="album_photos_files[]" multiple accept="image/*" class="w-full text-xs mb-2">
                <textarea name="album_photos_urls" rows="3" placeholder="/images/gallery/hero-medina.jpg" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">{{ is_array($place->album_photos) ? implode("\n", $place->album_photos) : '' }}</textarea>
            </div>
        </div>

        <!-- Contact & Location Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Opening Hours</label>
                <input type="text" name="opening_hours" value="{{ old('opening_hours', $place->opening_hours) }}" placeholder="09:00 AM - 10:00 PM" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Address</label>
                <input type="text" name="address" value="{{ old('address', $place->address) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Neighborhood</label>
                <input type="text" name="neighborhood" value="{{ old('neighborhood', $place->neighborhood) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $place->phone) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $place->whatsapp) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Website</label>
                <input type="text" name="website" value="{{ old('website', $place->website) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $place->email) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Google Map Search Query</label>
                <input type="text" name="map_query" value="{{ old('map_query', $place->map_query) }}" placeholder="e.g. Lina Ryad Chefchaouen" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Badge Text</label>
                <input type="text" name="badge_text" value="{{ old('badge_text', $place->badge_text) }}" placeholder="Local Favorite" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Direction Map Custom Google Maps Iframe (Optional - auto-generated from Map Query if empty)</label>
            <textarea name="direction_map_iframe" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono">{{ old('direction_map_iframe', $place->direction_map_iframe) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Amenities (Comma-separated)</label>
                <input type="text" name="amenities_text" value="{{ old('amenities_text', is_array($place->amenities) ? implode(', ', $place->amenities) : '') }}" placeholder="Rooftop Terrace, Wi-Fi, Halal" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Highlights (Comma-separated)</label>
                <input type="text" name="highlights_text" value="{{ old('highlights_text', is_array($place->highlights) ? implode(', ', $place->highlights) : '') }}" placeholder="Mountain View, Historic Architecture" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>

        <div class="flex flex-wrap gap-6 pt-2">
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_verified_partner" value="1" @checked(old('is_verified_partner', $place->is_verified_partner))> ✓ Verified Partner
            </label>
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $place->is_featured))> ★ Featured on Homepage
            </label>
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_hidden_gem" value="1" @checked(old('is_hidden_gem', $place->is_hidden_gem))> ✨ Hidden Gem of the Medina
            </label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.places.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">Save Place</button>
        </div>
    </form>
@endsection
