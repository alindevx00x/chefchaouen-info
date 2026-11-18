@extends('layouts.admin')

@section('header', 'Users Management (Platform Admins)')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-6 h-fit">
            <h2 class="font-bold text-base text-slate-900 mb-4">Add Administrator</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="name" required placeholder="Full Name" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                <input type="email" name="email" required placeholder="Admin Email" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                <select name="role" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="editor">Content Editor</option>
                </select>
                <input type="text" name="phone" placeholder="Phone (optional)" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                <input type="password" name="password" required placeholder="Password (min 8 chars)" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-chaouen-600 text-white text-xs font-bold">Create Admin</button>
            </form>
        </div>

        <div class="lg:col-span-8 space-y-4">
            @foreach($admins as $admin)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <form action="{{ route('admin.users.update', $admin) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-6 gap-3 items-end">
                        @csrf @method('PUT')
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Name</label>
                            <input type="text" name="name" value="{{ $admin->name }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Email</label>
                            <input type="email" name="email" value="{{ $admin->email }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Role</label>
                            <select name="role" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                                <option value="super_admin" @selected($admin->role === 'super_admin')>Super Admin</option>
                                <option value="admin" @selected($admin->role === 'admin')>Admin</option>
                                <option value="editor" @selected($admin->role === 'editor')>Editor</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">New Password</label>
                            <input type="password" name="password" placeholder="Optional" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                        <div class="sm:col-span-6 flex items-center justify-between pt-2 border-t border-slate-100">
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                <input type="checkbox" name="is_active" value="1" @checked($admin->is_active)> Active Account
                            </label>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-chaouen-600 text-white text-xs font-bold">Update Admin</button>
                                @if($admin->id !== auth()->id())
                                    <button type="submit" form="delete-admin-{{ $admin->id }}" onclick="return confirm('Remove this admin?')" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-xs font-bold">Delete</button>
                                @endif
                            </div>
                        </div>
                    </form>
                    @if($admin->id !== auth()->id())
                        <form id="delete-admin-{{ $admin->id }}" action="{{ route('admin.users.destroy', $admin) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
