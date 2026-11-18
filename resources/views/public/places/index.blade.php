@extends('layouts.public')

@section('title', 'Places to Visit in Chefchaouen | Chefchaouen.info')

@section('content')
    <section class="bg-gradient-to-b from-chaouen-900 to-chaouen-800 text-white py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold uppercase tracking-widest text-sky-300">YOUR CITY, YOUR PACE</span>
            <h1 class="font-editorial text-4xl sm:text-5xl font-bold mt-2 mb-3">Places to Visit in Chefchaouen</h1>
            <p class="text-chaouen-100 max-w-2xl text-sm sm:text-base">Explore verified restaurants, rooftop cafés, boutique riads, artisan shopping, historic landmarks, Rif hiking access, parking, and local services.</p>

            <form action="{{ route('places.index') }}" method="GET" class="mt-7 bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/20 flex flex-col sm:flex-row gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, neighborhood, or keyword..."
                       class="flex-1 px-4 py-2.5 rounded-xl bg-white text-slate-900 text-sm outline-none">
                <select name="category" class="sm:w-52 px-4 py-2.5 rounded-xl bg-white text-slate-900 text-sm outline-none">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }} ({{ $cat->places_count }})</option>
                    @endforeach
                </select>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-sm transition">
                    Filter
                </button>
            </form>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Category Filter Pills -->
        <div class="flex flex-wrap items-center gap-2 mb-8">
            <a href="{{ route('places.index') }}" class="px-4 py-2 rounded-full text-xs font-semibold border transition {{ !request('category') && !request('hidden_gems') && !request('verified') ? 'bg-chaouen-600 text-white border-chaouen-600' : 'bg-white text-slate-700 border-slate-200 hover:border-chaouen-400' }}">
                All Places
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('places.index', ['category' => $cat->slug]) }}" class="px-4 py-2 rounded-full text-xs font-semibold border transition {{ request('category') === $cat->slug ? 'bg-chaouen-600 text-white border-chaouen-600' : 'bg-white text-slate-700 border-slate-200 hover:border-chaouen-400' }}">
                    {{ $cat->name }} ({{ $cat->places_count }})
                </a>
            @endforeach
            <a href="{{ route('places.index', ['hidden_gems' => 1]) }}" class="px-4 py-2 rounded-full text-xs font-semibold border transition {{ request('hidden_gems') ? 'bg-amber-500 text-white border-amber-500' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                ✨ Hidden Gems
            </a>
            <a href="{{ route('places.index', ['verified' => 1]) }}" class="px-4 py-2 rounded-full text-xs font-semibold border transition {{ request('verified') ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-800 border-emerald-200' }}">
                ✓ Verified Partners
            </a>
        </div>

        @if($places->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center">
                <h3 class="text-lg font-bold text-slate-900">No places matched your filter</h3>
                <p class="text-sm text-slate-500 mt-1 mb-4">Try clearing your search or exploring another category.</p>
                <a href="{{ route('places.index') }}" class="inline-block px-5 py-2.5 rounded-full bg-chaouen-600 text-white text-xs font-semibold">Reset Filters</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($places as $place)
                    @include('public.partials.place-card', ['place' => $place])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $places->links() }}
            </div>
        @endif
    </section>
@endsection
