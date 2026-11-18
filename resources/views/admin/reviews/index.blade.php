@extends('layouts.admin')

@section('header', 'Reviews & Ratings Moderation')

@section('content')
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ !request('status') ? 'bg-chaouen-600 text-white' : 'bg-white border border-slate-200' }}">All</a>
        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ request('status') === 'pending' ? 'bg-amber-500 text-white' : 'bg-white border border-slate-200' }}">Pending Approval</a>
        <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ request('status') === 'approved' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200' }}">Approved</a>
        <a href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ request('status') === 'rejected' ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200' }}">Rejected</a>
    </div>

    <div class="space-y-4">
        @foreach($reviews as $review)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $review->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($review->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">{{ $review->status }}</span>
                            <span class="font-bold text-sm text-slate-900">{{ $review->visitor_name }} ({{ $review->visitor_email }})</span>
                            <span class="text-xs text-amber-600 font-bold">{{ str_repeat('★', $review->rating) }}</span>
                        </div>
                        <p class="text-xs text-chaouen-700 font-semibold mt-1">On: {{ $review->reviewable->title ?? 'Deleted Item' }}</p>
                        <p class="text-sm text-slate-700 mt-2">{{ $review->comment }}</p>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        @if($review->status !== 'approved')
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold">Approve & Publish</button>
                            </form>
                        @endif
                        @if($review->status !== 'rejected')
                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 text-xs font-bold">Reject</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete review?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-xs font-bold">Delete</button>
                        </form>
                    </div>
                </div>

                <!-- Admin Reply Form -->
                <form action="{{ route('admin.reviews.reply', $review) }}" method="POST" class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-slate-100">
                    @csrf
                    <input type="text" name="admin_reply" value="{{ $review->admin_reply }}" placeholder="Write an official admin reply (appears publicly below comment once approved)..."
                           class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    <label class="flex items-center gap-1.5 text-xs text-slate-600 px-2">
                        <input type="checkbox" name="approve_too" value="1" checked> Approve on reply
                    </label>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-chaouen-600 text-white text-xs font-bold">Save Reply</button>
                </form>
            </div>
        @endforeach
        <div>{{ $reviews->links() }}</div>
    </div>
@endsection
