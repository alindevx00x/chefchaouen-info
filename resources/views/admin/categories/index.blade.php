@extends('layouts.admin')

@section('header', 'Place Categories (Arched Doors)')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-6 h-fit">
            <h2 class="font-bold text-base text-slate-900 mb-4">Add New Category</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Category Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Subtitle</label>
                    <input type="text" name="subtitle" placeholder="e.g. Tagines & Rif Flavors" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Door Photo (Upload)</label>
                    <input type="file" name="door_image_file" accept="image/*" class="w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Or Image Path / URL</label>
                    <input type="text" name="door_image_url" placeholder="/images/gallery/medina-door.jpg" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="10" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">
                    Create Category
                </button>
            </form>
        </div>

        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase">
                        <tr>
                            <th class="px-4 py-3">Door</th>
                            <th class="px-4 py-3">Name & Subtitle</th>
                            <th class="px-4 py-3">Places</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($categories as $cat)
                            <tr>
                                <td class="px-4 py-3">
                                    <img src="{{ asset($cat->door_image ?: '/images/gallery/medina-door.jpg') }}" class="w-10 h-12 rounded-t-full rounded-b-md object-cover border border-slate-200">
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.categories.update', $cat) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $cat->name }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 font-bold text-slate-900 w-36">
                                        <input type="text" name="subtitle" value="{{ $cat->subtitle }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 w-40">
                                        <input type="number" name="sort_order" value="{{ $cat->sort_order }}" class="px-2 py-1.5 rounded-lg border border-slate-200 w-14">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-chaouen-600 text-white font-semibold">Save</button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 font-bold text-chaouen-700">{{ $cat->places_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
