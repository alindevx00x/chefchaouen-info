@extends('layouts.admin')

@section('header', 'Tourist Routes Management')

@section('content')
    <div x-data="{
            selectedIds: [],
            allIds: @js($routes->pluck('id')->values()),
            toggleAll(checked) {
                this.selectedIds = checked ? [...this.allIds] : [];
            },
            submitBulk(action) {
                if (this.selectedIds.length === 0) return;
                if (action === 'delete' && !confirm('Delete ' + this.selectedIds.length + ' selected route(s)?')) return;
                const form = document.getElementById('routes-bulk-form');
                form.querySelector('input[name=action]').value = action;
                form.querySelectorAll('input.bulk-id-input').forEach(el => el.remove());
                this.selectedIds.forEach(id => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'ids[]';
                    inp.value = id;
                    inp.className = 'bulk-id-input';
                    form.appendChild(inp);
                });
                form.submit();
            }
         }"
         class="space-y-4">

        <form id="routes-bulk-form" action="{{ route('admin.routes.bulk') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="action" value="">
        </form>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('admin.routes.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search route..." class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs w-52">
                <select name="route_type" class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    <option value="">All Route Types</option>
                    <option value="inside_city" @selected(request('route_type') === 'inside_city')>Inside City (داخل المدينة)</option>
                    <option value="outside_city" @selected(request('route_type') === 'outside_city')>Outside City (خارج المدينة)</option>
                </select>
                <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    <option value="">All Status</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold">Filter</button>
            </form>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.routes.export', request()->query()) }}" class="px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold">
                    Export CSV ↓
                </a>
                <a href="{{ route('admin.routes.create') }}" class="px-5 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">
                    + Add Tourist Route
                </a>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div x-show="selectedIds.length > 0" x-cloak x-transition
             class="bg-chaouen-900 text-white rounded-2xl px-4 py-3 flex flex-wrap items-center justify-between gap-3 shadow-md">
            <span class="text-xs font-bold">
                <span x-text="selectedIds.length"></span> route(s) selected
            </span>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="submitBulk('publish')" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition">Publish Selected</button>
                <button type="button" @click="submitBulk('archive')" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition">Archive / Move to Draft</button>
                <button type="button" @click="submitBulk('delete')" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition">Delete Selected</button>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox"
                                       :checked="allIds.length > 0 && selectedIds.length === allIds.length"
                                       @change="toggleAll($event.target.checked)"
                                       aria-label="Select all routes"
                                       class="rounded border-slate-300">
                            </th>
                            <th class="px-4 py-3">Route Name</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">From → To</th>
                            <th class="px-4 py-3">Difficulty &amp; Time</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($routes as $route)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                           value="{{ $route->id }}"
                                           x-model.number="selectedIds"
                                           aria-label="Select {{ $route->title }}"
                                           class="rounded border-slate-300">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset($route->featured_image ?: '/images/gallery/rif-mountains.jpg') }}" class="w-12 h-10 rounded-lg object-cover border border-slate-200">
                                        <div>
                                            <p class="font-bold text-slate-900 text-sm">{{ $route->title }}</p>
                                            <p class="text-[11px] text-chaouen-600">{{ $route->title_ar }} @if($route->suggested_by_name) • Draft by: {{ $route->suggested_by_name }} @endif</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full font-semibold {{ $route->route_type === 'outside_city' ? 'bg-emerald-50 text-emerald-700' : 'bg-chaouen-50 text-chaouen-700' }}">
                                        {{ $route->route_type_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $route->start_point }} → {{ $route->end_point }}</td>
                                <td class="px-4 py-3"><strong>{{ $route->difficulty }}</strong> • {{ $route->estimated_walking_time }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-full font-bold {{ $route->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ ucfirst($route->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($route->status !== 'published')
                                            <form action="{{ route('admin.routes.publish', $route) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white font-semibold">Publish</button>
                                            </form>
                                        @else
                                            <a href="{{ route('routes.show', $route->slug) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold">View</a>
                                        @endif
                                        <a href="{{ route('admin.routes.edit', $route) }}" class="px-2.5 py-1.5 rounded-lg bg-chaouen-600 text-white font-semibold">Edit</a>
                                        <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" onsubmit="return confirm('Delete this route?')">
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
            <div class="p-4 border-t border-slate-100">{{ $routes->links() }}</div>
        </div>
    </div>
@endsection
