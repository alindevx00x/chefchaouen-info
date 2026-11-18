<article class="group bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition duration-300 flex flex-col">
    <a href="{{ route('places.show', $place->slug) }}" class="relative aspect-[4/3] overflow-hidden bg-slate-100 block">
        <img src="{{ asset($place->featured_image ?: '/images/gallery/medina-door.jpg') }}"
             alt="{{ $place->title }}"
             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
            @if($place->category)
                <span class="px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-sm text-[11px] font-bold text-chaouen-800 shadow-sm">
                    {{ $place->category->name }}
                </span>
            @endif
            @if($place->is_hidden_gem)
                <span class="px-2.5 py-1 rounded-full bg-amber-500 text-white text-[11px] font-bold shadow-sm">
                    Hidden Gem
                </span>
            @endif
        </div>
        @if($place->is_verified_partner)
            <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-emerald-600/95 text-white text-[10px] font-bold shadow-sm">
                ✓ Verified Partner
            </span>
        @endif
    </a>
    <div class="p-4 flex-1 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between gap-2 text-xs text-slate-500 mb-1.5">
                <span>{{ $place->neighborhood ?: 'Old Medina' }}</span>
                <span class="font-semibold text-amber-600">★ {{ number_format($place->rating_avg, 1) }}</span>
            </div>
            <h3 class="font-bold text-base text-slate-900 group-hover:text-chaouen-600 transition line-clamp-1">
                <a href="{{ route('places.show', $place->slug) }}">{{ $place->title }}</a>
            </h3>
            <p class="text-xs text-slate-600 mt-1.5 line-clamp-2 leading-relaxed">
                {{ $place->short_description }}
            </p>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="font-semibold text-slate-700">{{ $place->price_range ?: '$$' }}</span>
            <a href="{{ route('places.show', $place->slug) }}" class="font-semibold text-chaouen-600 hover:text-chaouen-800">
                Explore →
            </a>
        </div>
    </div>
</article>
