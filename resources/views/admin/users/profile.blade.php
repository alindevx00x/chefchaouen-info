@extends('layouts.admin')

@section('header', 'My Profile Settings')

@section('content')
    <form action="{{ route('admin.profile.update') }}" method="POST" class="max-w-2xl bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-5">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Admin Bio</label>
            <textarea name="bio" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">{{ old('bio', $user->bio) }}</textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-100">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
        </div>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">Save Profile</button>
    </form>
@endsection
