@extends('layouts.public')

@section('title', 'Suggest a Place or Route in Chefchaouen | Chefchaouen.info')

@section('content')
    <section class="bg-gradient-to-b from-chaouen-900 to-chaouen-800 text-white py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold uppercase tracking-widest text-sky-300">MADE WITH CARE, FOR THE PEOPLE WHO CALL IT HOME</span>
            <h1 class="font-editorial text-4xl sm:text-5xl font-bold mt-2">Suggest a Local Place or Route</h1>
            <p class="text-chaouen-100 mt-2 text-sm sm:text-base">Share a beloved café, family riad, artisan shop, or scenic Rif trail. Submissions enter our editorial dashboard as drafts under your name for review and publication.</p>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ kind: 'place' }">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-200 p-7 sm:p-9 shadow-sm">
            <div class="flex gap-3 mb-7 pb-4 border-b border-slate-100">
                <button type="button" @click="kind = 'place'"
                        :class="kind === 'place' ? 'bg-chaouen-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        class="px-5 py-2.5 rounded-full text-sm font-bold transition">
                    🏛️ Suggest a Place
                </button>
                <button type="button" @click="kind = 'route'"
                        :class="kind === 'route' ? 'bg-chaouen-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        class="px-5 py-2.5 rounded-full text-sm font-bold transition">
                    🥾 Suggest a Tourist Route
                </button>
            </div>

            <!-- Place Suggestion Form -->
            <form x-show="kind === 'place'" action="{{ route('suggest.submit') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="suggestion_type" value="place">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Your Name *</label>
                        <input type="text" name="suggested_by_name" required placeholder="Who is suggesting this place?" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Your Email *</label>
                        <input type="email" name="suggested_by_email" required placeholder="you@example.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Place Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Café Dar Blue" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Category *</label>
                        <select name="place_category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:border-chaouen-500">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Neighborhood</label>
                        <input type="text" name="neighborhood" placeholder="e.g. El Haouta, Bab Ssour" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Opening Hours</label>
                        <input type="text" name="opening_hours" placeholder="e.g. 09:00 - 22:00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Price Range</label>
                        <select name="price_range" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none">
                            <option value="Free">Free</option>
                            <option value="$">$ (Budget)</option>
                            <option value="$$" selected>$$ (Moderate)</option>
                            <option value="$$$">$$$ (Boutique / Luxury)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Address / Landmark</label>
                        <input type="text" name="address" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Phone / WhatsApp (optional)</label>
                        <input type="text" name="whatsapp" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Description & What Makes It Special *</label>
                    <textarea name="short_description" rows="4" required placeholder="Tell visitors why this spot deserves to be on Chefchaouen.info..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Additional Notes for Admin (optional)</label>
                    <input type="text" name="suggested_notes" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                </div>

                <button type="submit" class="px-7 py-3 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-sm font-bold transition">
                    Submit Place Draft for Review →
                </button>
            </form>

            <!-- Route Suggestion Form -->
            <form x-show="kind === 'route'" x-cloak action="{{ route('suggest.submit') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="suggestion_type" value="route">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Your Name *</label>
                        <input type="text" name="suggested_by_name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Your Email *</label>
                        <input type="email" name="suggested_by_email" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Route Name *</label>
                        <input type="text" name="title" required placeholder="e.g. Old Tannery to Ras El Maa Loop" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Route Type *</label>
                        <select name="route_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none">
                            <option value="inside_city">Inside City (داخل المدينة)</option>
                            <option value="outside_city">Outside City (خارج المدينة)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Start Point *</label>
                        <input type="text" name="start_point" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">End Point *</label>
                        <input type="text" name="end_point" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Difficulty & Est. Time</label>
                        <div class="flex gap-2">
                            <select name="difficulty" class="w-1/2 px-2 py-2.5 rounded-xl border border-slate-200 bg-white text-xs outline-none">
                                <option value="Easy">Easy</option>
                                <option value="Moderate">Moderate</option>
                                <option value="Challenging">Challenging</option>
                            </select>
                            <input type="text" name="estimated_walking_time" placeholder="2 hours" class="w-1/2 px-2.5 py-2.5 rounded-xl border border-slate-200 text-xs outline-none">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Route Description & Waypoints *</label>
                    <textarea name="short_description" rows="4" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none"></textarea>
                </div>

                <button type="submit" class="px-7 py-3 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-sm font-bold transition">
                    Submit Route Draft for Review →
                </button>
            </form>
        </div>
    </section>
@endsection
