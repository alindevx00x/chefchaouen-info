@extends('layouts.admin')

@section('header', $post->exists ? 'Edit Blog Post' : 'Write New Blog Post')

@section('content')
    <form action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}"
          method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
        @csrf
        @if($post->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Post Title *</label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category</label>
                <select name="blog_category_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm">
                    <option value="">Uncategorized</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('blog_category_id', $post->blog_category_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Excerpt</label>
                <textarea name="excerpt" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Featured Image</label>
                <input type="file" name="featured_image_file" accept="image/*" class="text-xs mb-1.5 w-full">
                <input type="text" name="featured_image_url" value="{{ old('featured_image_url', $post->featured_image) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Article Body (TinyMCE Self-Hosted CDN)</label>
            <textarea name="content" class="tinymce-editor">{{ old('content', $post->content) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Reading Time (Minutes)</label>
                <input type="number" name="reading_time_minutes" value="{{ old('reading_time_minutes', $post->reading_time_minutes) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">SEO Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">SEO Meta Description</label>
                <input type="text" name="meta_description" value="{{ old('meta_description', $post->meta_description) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-xs font-semibold"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published))> Published</label>
            <label class="flex items-center gap-2 text-xs font-semibold"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post->is_featured))> Featured</label>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.blog.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">Save Blog Post</button>
        </div>
    </form>
@endsection
