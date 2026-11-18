@extends('layouts.admin')

@section('header', 'Public Pages (Static Content & PDFs)')

@section('content')
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-600">Manage About, History & Culture, Terms, Cookie Policy, Return & Refund Policy, Disclaimer, and attached PDF documents.</p>
        <a href="{{ route('admin.pages.create') }}" class="px-5 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">+ Add Public Page</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase">
                    <tr>
                        <th class="px-4 py-3">Page Title</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">PDF Attachments</th>
                        <th class="px-4 py-3">Visibility</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($pages as $page)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <p class="font-bold text-sm text-slate-900">{{ $page->title }}</p>
                                <p class="text-[11px] text-slate-500">{{ $page->subtitle }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">/page/{{ $page->slug }}</td>
                            <td class="px-4 py-3">
                                @if(is_array($page->pdf_attachments) && count($page->pdf_attachments) > 0)
                                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 font-bold">{{ count($page->pdf_attachments) }} PDF(s)</span>
                                @else
                                    <span class="text-slate-400">None</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 space-x-1">
                                @if($page->is_published)<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-semibold">Published</span>@endif
                                @if($page->is_gdpr_page)<span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 font-semibold">GDPR</span>@endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold">View</a>
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="px-2.5 py-1.5 rounded-lg bg-chaouen-600 text-white font-semibold">Edit</a>
                                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Delete this public page?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
