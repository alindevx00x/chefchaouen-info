@extends('layouts.admin')

@section('header', 'Newsletter Subscribers')

@section('content')
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-600">Total subscribers: <strong>{{ $subscribers->total() }}</strong></p>
        <a href="{{ route('admin.newsletters.export') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">
            ⬇ Export Subscribers as CSV
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase">
                <tr>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Subscribed At</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($subscribers as $sub)
                    <tr>
                        <td class="px-4 py-3 font-bold text-slate-900">{{ $sub->email }}</td>
                        <td class="px-4 py-3">{{ $sub->name ?: '-' }}</td>
                        <td class="px-4 py-3"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">{{ ucfirst($sub->status) }}</span></td>
                        <td class="px-4 py-3 text-slate-500">{{ $sub->created_at?->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('admin.newsletters.destroy', $sub) }}" method="POST" onsubmit="return confirm('Remove subscriber?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1 rounded-lg bg-rose-50 text-rose-700 font-semibold">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">{{ $subscribers->links() }}</div>
    </div>
@endsection
