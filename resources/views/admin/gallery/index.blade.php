@extends('layouts.admin')

@section('header', 'Gallery Photos Manager')

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="font-bold text-base text-slate-900 mb-4">Upload Photo to Platform Gallery</h2>
        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Photo Title *</label>
                <input type="text" name="title" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Category *</label>
                <select name="category" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    <option value="Medina">Medina</option>
                    <option value="Nature & Akchour">Nature & Akchour</option>
                    <option value="Architecture">Architecture</option>
                    <option value="Culture & Crafts">Culture & Crafts</option>
                    <option value="Gastronomy">Gastronomy</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Location Tag</label>
                <input type="text" name="location_tag" placeholder="e.g. El Haouta" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Upload Image *</label>
                <input type="file" name="image_file" accept="image/*" class="w-full text-xs">
            </div>
            <button type="submit" class="py-2.5 px-4 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">
                + Upload Photo
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($photos as $photo)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <img src="{{ asset($photo->image_path) }}" class="aspect-[4/3] w-full object-cover">
                <div class="p-4 space-y-2">
                    <form action="{{ route('admin.gallery.update', $photo) }}" method="POST" class="space-y-2">
                        @csrf @method('PUT')
                        <input type="text" name="title" value="{{ $photo->title }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-bold">
                        <div class="flex gap-2">
                            <input type="text" name="category" value="{{ $photo->category }}" class="w-1/2 px-2 py-1 rounded-lg border border-slate-200 text-[11px]">
                            <input type="text" name="location_tag" value="{{ $photo->location_tag }}" class="w-1/2 px-2 py-1 rounded-lg border border-slate-200 text-[11px]">
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <label class="text-[11px] flex items-center gap-1"><input type="checkbox" name="is_featured" value="1" @checked($photo->is_featured)> Home</label>
                            <div class="flex gap-1.5">
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-chaouen-600 text-white text-[11px] font-semibold">Save</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route('admin.gallery.destroy', $photo) }}" method="POST" onsubmit="return confirm('Remove photo?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 text-[11px] font-semibold">Delete Photo</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
