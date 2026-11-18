@extends('layouts.admin')

@section('header', 'Blog Posts & Categories')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Categories Sidebar -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="font-bold text-sm text-slate-900 mb-3">Blog Categories</h3>
                <form action="{{ route('admin.blog.categories.store') }}" method="POST" class="flex gap-2 mb-4">
                    @csrf
                    <input type="text" name="name" required placeholder="New category name" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-chaouen-600 text-white text-xs font-bold">Add</button>
                </form>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                            <span class="font-semibold">{{ $cat->name }} ({{ $cat->posts_count }})</span>
                            <form action="{{ route('admin.blog.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Delete category?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:underline font-semibold">Delete</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Posts Table -->
        <div class="lg:col-span-8 space-y-4">
            <div class="flex justify-end">
                <a href="{{ route('admin.blog.create') }}" class="px-5 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">+ Write Blog Post</a>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase">
                        <tr>
                            <th class="px-4 py-3">Post Title</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($posts as $post)
                            <tr>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $post->title }}</td>
                                <td class="px-4 py-3">{{ $post->category->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full font-bold {{ $post->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $post->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex gap-1.5">
                                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-700 font-semibold">View</a>
                                        <a href="{{ route('admin.blog.edit', $post) }}" class="px-2.5 py-1.5 rounded-lg bg-chaouen-600 text-white font-semibold">Edit</a>
                                        <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete post?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 font-semibold">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 border-t border-slate-100">{{ $posts->links() }}</div>
            </div>
        </div>
    </div>
@endsection
