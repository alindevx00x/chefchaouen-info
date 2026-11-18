@extends('layouts.public')

@section('title', 'Tourist Routes in Chefchaouen | Inside Medina & Rif Mountains')

@section('content')
    <section class="bg-gradient-to-b from-chaouen-900 to-sky-900 text-white py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold uppercase tracking-widest text-sky-300">TAKE THE SCENIC WAY</span>
            <h1 class="font-editorial text-4xl sm:text-5xl font-bold mt-2 mb-3">Chefchaouen Tourist Routes</h1>
            <p class="text-chaouen-100 max-w-2xl text-sm sm:text-base">Choose between Inside City walks through the blue medina and Outside City hiking trails across Akchour and the Rif peaks.</p>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('routes.index') }}" class="px-4 py-2 rounded-full text-xs font-bold transition {{ !request('type') ? 'bg-white text-chaouen-900' : 'bg-white/15 text-white hover:bg-white/25' }}">
                    All Routes ({{ $routes->count() }})
                </a>
                <a href="{{ route('routes.index', ['type' => 'inside_city']) }}" class="px-4 py-2 rounded-full text-xs font-bold transition {{ request('type') === 'inside_city' ? 'bg-white text-chaouen-900' : 'bg-white/15 text-white hover:bg-white/25' }}">
                    🏛️ Inside City (داخل المدينة)
                </a>
                <a href="{{ route('routes.index', ['type' => 'outside_city']) }}" class="px-4 py-2 rounded-full text-xs font-bold transition {{ request('type') === 'outside_city' ? 'bg-white text-chaouen-900' : 'bg-white/15 text-white hover:bg-white/25' }}">
                    ⛰️ Outside City / Province (خارج المدينة)
                </a>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @php
            $insideRoutes = $routes->where('route_type', 'inside_city');
            $outsideRoutes = $routes->where('route_type', 'outside_city');
        @endphp

        @if($insideRoutes->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-chaouen-600">Inside City · مسارات داخل المدينة</span>
                        <h2 class="font-editorial text-3xl font-bold text-slate-900 mt-1">Medina & Heritage Walks</h2>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($insideRoutes as $route)
                        @include('public.partials.route-card', ['route' => $route])
                    @endforeach
                </div>
            </div>
        @endif

        @if($outsideRoutes->isNotEmpty())
            <div>
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-emerald-700">Outside City (Province) · مسارات خارج المدينة</span>
                        <h2 class="font-editorial text-3xl font-bold text-slate-900 mt-1">Rif Mountains & Akchour Waterfalls</h2>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($outsideRoutes as $route)
                        @include('public.partials.route-card', ['route' => $route])
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
