@extends('layouts.admin')

@section('header', $page->exists ? 'Edit Page: ' . $page->title : 'Create Public Page')

@section('content')
    <form action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}"
          method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
        @csrf
        @if($page->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Page Title *</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $page->slug) }}" placeholder="auto-generated-from-title" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Badge</label>
                <input type="text" name="hero_badge" value="{{ old('hero_badge', $page->hero_badge) }}" placeholder="e.g. Historical Guide" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Subtitle</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $page->subtitle) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Featured Image (Upload or Path)</label>
                <div class="flex gap-2">
                    <input type="file" name="featured_image_file" accept="image/*" class="text-xs w-1/2">
                    <input type="text" name="featured_image_url" value="{{ old('featured_image_url', $page->featured_image) }}" class="w-1/2 px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Full Page Content (TinyMCE Self-Hosted CDN)</label>
            <textarea name="content" class="tinymce-editor">{{ old('content', $page->content) }}</textarea>
        </div>

        <!-- Downloadable PDF Attachments Section -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
            <div>
                <h3 class="font-bold text-sm text-slate-900">Downloadable PDF Attachments</h3>
                <p class="text-xs text-slate-500">Attach PDF guides, brochures, or policy documents to appear as download cards on this page.</p>
            </div>

            @if(is_array($page->pdf_attachments) && count($page->pdf_attachments) > 0)
                <div class="space-y-2">
                    @foreach($page->pdf_attachments as $idx => $pdf)
                        <div class="flex items-center justify-between bg-white px-4 py-2.5 rounded-xl border border-slate-200 text-xs">
                            <div>
                                <span class="font-bold text-slate-800">{{ $pdf['title'] ?? 'PDF Document' }}</span>
                                <span class="text-slate-400 ml-2">({{ $pdf['file_size'] ?? '' }})</span>
                            </div>
                            <label class="flex items-center gap-1.5 text-rose-600 font-semibold cursor-pointer">
                                <input type="checkbox" name="remove_pdfs[]" value="{{ $idx }}"> Remove
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Upload PDF File</label>
                    <input type="file" name="new_pdf_files[]" accept="application/pdf" class="w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">PDF Display Title</label>
                    <input type="text" name="new_pdf_titles[]" placeholder="e.g. Medina Walking Map PDF" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Short Description</label>
                    <input type="text" name="new_pdf_descriptions[]" placeholder="Printable offline brochure" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">SEO Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">SEO Meta Description</label>
                <input type="text" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>

        <div class="flex flex-wrap gap-6 pt-2">
            <label class="flex items-center gap-2 text-xs font-semibold"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published))> Published</label>
            <label class="flex items-center gap-2 text-xs font-semibold"><input type="checkbox" name="show_in_header" value="1" @checked(old('show_in_header', $page->show_in_header))> Show in Header</label>
            <label class="flex items-center gap-2 text-xs font-semibold"><input type="checkbox" name="show_in_footer" value="1" @checked(old('show_in_footer', $page->show_in_footer))> Show in Footer</label>
            <label class="flex items-center gap-2 text-xs font-semibold"><input type="checkbox" name="is_gdpr_page" value="1" @checked(old('is_gdpr_page', $page->is_gdpr_page))> GDPR Linked Page</label>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.pages.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">Save Public Page</button>
        </div>
    </form>
@endsection
