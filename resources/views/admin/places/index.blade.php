@extends('layouts.admin')

@section('header', 'Places Directory')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('admin.places.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title or suggested by..." class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs w-56">
            <select name="category" class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                <option value="">All Categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                <option value="">All Status</option>
                <option value="published" @selected(request('status') === 'published')>Published</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft (Suggestions)</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold">Filter</button>
        </form>

        <a href="{{ route('admin.places.create') }}" class="px-5 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">
            + Add New Place
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase">
                    <tr>
                        <th class="px-4 py-3">Place</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Price / Contact</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($places as $place)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset($place->featured_image ?: '/images/gallery/medina-door.jpg') }}" class="w-12 h-10 rounded-lg object-cover border border-slate-200">
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $place->title }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $place->neighborhood }} @if($place->suggested_by_name) • <span class="text-amber-700 font-semibold">Suggested by: {{ $place->suggested_by_name }}</span> @endif</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full bg-chaouen-50 text-chaouen-800 font-semibold">{{ $place->category->name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="font-bold">{{ $place->price_range }}</span> • {{ $place->opening_hours ?: 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full font-bold {{ $place->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($place->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($place->status !== 'published')
                                        <form action="{{ route('admin.places.publish', $place) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white font-semibold">Publish</button>
                                        </form>
                                    @else
                                        <a href="{{ route('places.show', $place->slug) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold">View</a>
                                    @endif
                                    <a href="{{ route('admin.places.edit', $place) }}" class="px-2.5 py-1.5 rounded-lg bg-chaouen-600 text-white font-semibold">Edit</a>
                                    <form action="{{ route('admin.places.destroy', $place) }}" method="POST" onsubmit="return confirm('Delete this place?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $places->links() }}</div>
    </div>
@endsection
