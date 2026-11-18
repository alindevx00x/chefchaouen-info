<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with(['category', 'author'])->latest()->paginate(15);
        $categories = BlogCategory::withCount('posts')->get();
        return view('admin.blog.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.form', [
            'post' => new BlogPost(['is_published' => true, 'reading_time_minutes' => 4]),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $post = new BlogPost();
        $this->savePost($request, $post, true);
        return redirect()->route('admin.blog.index')->with('success', 'Blog post published.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.form', compact('post', 'categories'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $this->savePost($request, $post, false);
        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return back()->with('success', 'Blog post deleted.');
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
        ]);

        BlogCategory::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(3)),
            'description' => $data['description'] ?? null,
        ]);

        return back()->with('success', 'Blog category created.');
    }

    public function destroyCategory(BlogCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Blog category deleted.');
    }

    protected function savePost(Request $request, BlogPost $post, bool $isNew): void
    {
        $data = $request->validate([
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'title' => 'required|string|max:180',
            'excerpt' => 'nullable|string|max:600',
            'content' => 'nullable|string',
            'featured_image_url' => 'nullable|string|max:255',
            'featured_image_file' => 'nullable|image|max:5120',
            'reading_time_minutes' => 'nullable|integer|min:1|max:60',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $featuredImage = $data['featured_image_url'] ?: ($post->featured_image ?: '/images/gallery/blue-stairs.jpg');
        if ($request->hasFile('featured_image_file')) {
            $featuredImage = '/storage/' . $request->file('featured_image_file')->store('uploads/blog', 'public');
        }

        $slug = $post->slug ?: Str::slug($data['title']);
        if ($isNew && BlogPost::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $post->fill([
            'blog_category_id' => $data['blog_category_id'] ?? null,
            'author_id' => $post->author_id ?: Auth::id(),
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'featured_image' => $featuredImage,
            'reading_time_minutes' => $data['reading_time_minutes'] ?? 4,
            'meta_title' => $data['meta_title'] ?? $data['title'],
            'meta_description' => $data['meta_description'] ?? $data['excerpt'],
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published'),
            'published_at' => $post->published_at ?: now(),
        ])->save();
    }
}
