<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicPage;
use App\Services\SeoFeedService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicPageController extends Controller
{
    public function index()
    {
        $pages = PublicPage::orderBy('sort_order')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.form', [
            'page' => new PublicPage(['is_published' => true, 'show_in_footer' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $page = new PublicPage();
        $this->savePage($request, $page, true);
        return redirect()->route('admin.pages.index')->with('success', 'Public page created successfully.');
    }

    public function edit(PublicPage $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, PublicPage $page)
    {
        $this->savePage($request, $page, false);
        return redirect()->route('admin.pages.index')->with('success', 'Public page updated successfully.');
    }

    public function destroy(PublicPage $page)
    {
        $page->delete();
        SeoFeedService::flushCaches();
        return back()->with('success', 'Public page deleted.');
    }

    protected function savePage(Request $request, PublicPage $page, bool $isNew): void
    {
        $data = $request->validate([
            'title' => 'required|string|max:180',
            'slug' => 'nullable|string|max:180',
            'subtitle' => 'nullable|string|max:255',
            'hero_badge' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'featured_image_url' => 'nullable|string|max:255',
            'featured_image_file' => 'nullable|image|max:5120',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'new_pdf_files.*' => 'nullable|mimes:pdf|max:20480',
            'new_pdf_titles.*' => 'nullable|string|max:180',
            'new_pdf_descriptions.*' => 'nullable|string|max:255',
        ]);

        $featuredImage = ($data['featured_image_url'] ?? null) ?: $page->featured_image;
        if ($request->hasFile('featured_image_file')) {
            $featuredImage = '/storage/' . $request->file('featured_image_file')->store('uploads/pages', 'public');
        }

        // Keep existing PDFs unless removed via checkboxes
        $existingPdfs = is_array($page->pdf_attachments) ? $page->pdf_attachments : [];
        $removeIndices = $request->input('remove_pdfs', []);
        $pdfs = [];
        foreach ($existingPdfs as $idx => $pdf) {
            if (!in_array((string) $idx, array_map('strval', $removeIndices), true)) {
                $pdfs[] = $pdf;
            }
        }

        // Attach newly uploaded PDFs
        if ($request->hasFile('new_pdf_files')) {
            $titles = $request->input('new_pdf_titles', []);
            $descriptions = $request->input('new_pdf_descriptions', []);
            foreach ($request->file('new_pdf_files') as $i => $file) {
                if (!$file) continue;
                $stored = $file->store('uploads/pdfs', 'public');
                $sizeKb = round($file->getSize() / 1024);
                $pdfs[] = [
                    'title' => !empty($titles[$i]) ? $titles[$i] : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'file_path' => '/storage/' . $stored,
                    'file_size' => $sizeKb >= 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB',
                    'description' => $descriptions[$i] ?? 'Downloadable PDF document',
                ];
            }
        }

        $slug = $request->filled('slug') ? Str::slug($data['slug']) : ($page->slug ?: Str::slug($data['title']));
        if ($isNew && PublicPage::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $page->fill([
            'title' => $data['title'],
            'slug' => $slug,
            'subtitle' => $data['subtitle'] ?? null,
            'hero_badge' => $data['hero_badge'] ?? null,
            'content' => $data['content'] ?? null,
            'featured_image' => $featuredImage,
            'pdf_attachments' => $pdfs,
            'meta_title' => $data['meta_title'] ?? $data['title'],
            'meta_description' => $data['meta_description'] ?? null,
            'show_in_header' => $request->boolean('show_in_header'),
            'show_in_footer' => $request->boolean('show_in_footer'),
            'is_gdpr_page' => $request->boolean('is_gdpr_page'),
            'is_published' => $request->boolean('is_published'),
            'sort_order' => $data['sort_order'] ?? 0,
        ])->save();

        SeoFeedService::flushCaches();
    }
}
