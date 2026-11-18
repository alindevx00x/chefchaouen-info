@extends('layouts.admin')

@section('header', 'Contact Submissions')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex gap-2">
            <a href="{{ route('admin.contacts.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ !request('status') ? 'bg-chaouen-600 text-white' : 'bg-white border border-slate-200' }}">All</a>
            <a href="{{ route('admin.contacts.index', ['status' => 'new']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ request('status') === 'new' ? 'bg-amber-500 text-white' : 'bg-white border border-slate-200' }}">New</a>
            <a href="{{ route('admin.contacts.index', ['status' => 'replied']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ request('status') === 'replied' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200' }}">Replied</a>
        </div>
        <a href="{{ route('admin.contacts.export') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">
            ⬇ Export Submissions CSV
        </a>
    </div>

    <div class="space-y-4">
        @foreach($contacts as $contact)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $contact->status === 'replied' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $contact->status }}</span>
                        <span class="text-xs font-bold text-slate-900 ml-2">{{ $contact->name }} ({{ $contact->email }})</span>
                        <span class="text-xs text-slate-400 ml-2">{{ $contact->created_at?->diffForHumans() }}</span>
                        <h3 class="font-bold text-sm text-chaouen-800 mt-1">{{ $contact->subject }} - <span class="text-slate-500 font-normal">{{ $contact->inquiry_type }}</span></h3>
                        <p class="text-sm text-slate-700 mt-2">{{ $contact->message }}</p>
                    </div>
                    <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete contact submission?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-xs font-bold">Delete</button>
                    </form>
                </div>

                <form action="{{ route('admin.contacts.reply', $contact) }}" method="POST" class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row gap-2">
                    @csrf
                    <textarea name="admin_reply" rows="2" required placeholder="Type your email reply to {{ $contact->email }}..." class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">{{ $contact->admin_reply }}</textarea>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold self-end sm:self-center">
                        Send Email Reply
                    </button>
                </form>
            </div>
        @endforeach
        <div>{{ $contacts->links() }}</div>
    </div>
@endsection
