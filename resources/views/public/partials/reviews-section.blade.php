<div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h2 class="font-editorial text-2xl font-bold text-slate-900">Visitor Reviews & Ratings</h2>
            <p class="text-xs text-slate-500 mt-0.5">All submitted comments and ratings are reviewed by our team before publishing.</p>
        </div>
        <div class="px-4 py-2 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-bold">
            ★ {{ number_format($ratingAvg, 1) }} / 5.0
        </div>
    </div>

    @if(session('review_success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
            {{ session('review_success') }}
        </div>
    @endif

    <!-- Approved Reviews List -->
    @if($reviews->isEmpty())
        <p class="text-sm text-slate-500 italic">No approved reviews yet. Be the first traveler to share your experience!</p>
    @else
        <div class="space-y-4">
            @foreach($reviews as $rev)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <div>
                            <span class="font-bold text-sm text-slate-900">{{ $rev->visitor_name }}</span>
                            @if($rev->visitor_country)
                                <span class="text-xs text-slate-500 ml-1.5">• {{ $rev->visitor_country }}</span>
                            @endif
                        </div>
                        <span class="text-xs font-bold text-amber-600">
                            {{ str_repeat('★', $rev->rating) }}{{ str_repeat('☆', 5 - $rev->rating) }}
                        </span>
                    </div>
                    @if($rev->title)
                        <h4 class="font-semibold text-sm text-slate-900 mb-1">{{ $rev->title }}</h4>
                    @endif
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $rev->comment }}</p>

                    @if($rev->admin_reply)
                        <div class="mt-3 ml-4 pl-3 border-l-2 border-chaouen-500 text-xs text-slate-700 bg-white rounded-r-xl p-3">
                            <span class="font-bold text-chaouen-700 block mb-0.5">Chefchaouen.info Editorial Reply:</span>
                            {{ $rev->admin_reply }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Leave a Review Form -->
    <div class="pt-6 border-t border-slate-100">
        <h3 class="font-bold text-base text-slate-900 mb-4">Add Your Review & Rating</h3>
        <form action="{{ route('reviews.submit') }}" method="POST" class="space-y-4">
            @csrf
            @include('public.partials.honeypot')
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="id" value="{{ $targetId }}">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Your Name *</label>
                    <input type="text" name="visitor_name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address *</label>
                    <input type="email" name="visitor_email" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Country (optional)</label>
                    <input type="text" name="visitor_country" placeholder="e.g. Spain, Morocco" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rating *</label>
                    <select name="rating" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:border-chaouen-500">
                        <option value="5">★★★★★ (5/5 Exceptional)</option>
                        <option value="4">★★★★☆ (4/5 Great)</option>
                        <option value="3">★★★☆☆ (3/5 Good)</option>
                        <option value="2">★★☆☆☆ (2/5 Fair)</option>
                        <option value="1">★☆☆☆☆ (1/5 Poor)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Review Title (optional)</label>
                    <input type="text" name="title" placeholder="Summarize your experience" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Your Comment *</label>
                <textarea name="comment" rows="3" required placeholder="Share helpful tips for fellow travelers..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500"></textarea>
            </div>

            <button type="submit" class="px-6 py-3 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-sm font-semibold transition">
                Submit Review for Moderation
            </button>
        </form>
    </div>
</div>
