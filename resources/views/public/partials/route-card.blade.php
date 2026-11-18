<article class="group bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition duration-300 flex flex-col">
    <a href="{{ route('routes.show', $route->slug) }}" class="relative aspect-[4/3] overflow-hidden bg-slate-100 block">
        <img src="{{ asset($route->featured_image ?: '/images/gallery/rif-mountains.jpg') }}"
             alt="{{ $route->title }}"
             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
            <span class="px-2.5 py-1 rounded-full {{ $route->route_type === 'outside_city' ? 'bg-emerald-700 text-white' : 'bg-chaouen-700 text-white' }} text-[10px] font-bold shadow-sm">
                {{ $route->route_type === 'outside_city' ? 'Outside City' : 'Inside Medina' }}
            </span>
            <span class="px-2.5 py-1 rounded-full bg-white/95 text-slate-800 text-[10px] font-bold shadow-sm">
                {{ $route->difficulty }}
            </span>
        </div>
    </a>
    <div class="p-4 flex-1 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between gap-2 text-xs text-slate-500 mb-1.5">
                <span>⏱ {{ $route->estimated_walking_time ?: '2 hours' }}</span>
                @if($route->distance_km)
                    <span>📏 {{ $route->distance_km }}</span>
                @endif
            </div>
            <h3 class="font-bold text-base text-slate-900 group-hover:text-chaouen-600 transition line-clamp-1">
                <a href="{{ route('routes.show', $route->slug) }}">{{ $route->title }}</a>
            </h3>
            @if($route->title_ar)
                <p class="text-xs font-medium text-chaouen-600 mt-0.5">{{ $route->title_ar }}</p>
            @endif
            <p class="text-xs text-slate-600 mt-1.5 line-clamp-2 leading-relaxed">
                {{ $route->short_description }}
            </p>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
            <button type="button"
                    @click="$dispatch('open-chaouen-chat', { prompt: @js($route->ai_prompt_question ?: 'How do I hike ' . $route->title . '?') })"
                    class="text-[11px] font-semibold text-chaouen-700 bg-chaouen-50 hover:bg-chaouen-100 px-2.5 py-1 rounded-full transition">
                ✨ Ask AI
            </button>
            <a href="{{ route('routes.show', $route->slug) }}" class="text-xs font-semibold text-chaouen-600 hover:text-chaouen-800">
                View Route →
            </a>
        </div>
    </div>
</article>
