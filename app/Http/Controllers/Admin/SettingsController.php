<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use App\Models\Faq;
use App\Models\Setting;
use App\Services\AiService;
use App\Services\DynamicMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'general');
        $settings = Setting::pluck('value', 'key')->toArray();
        $aiProviders = AiProvider::orderByDesc('is_default')->orderBy('id')->get();
        $faqs = Faq::orderBy('sort_order')->get();

        return view('admin.settings.index', compact('tab', 'settings', 'aiProviders', 'faqs'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_icon_file' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg,gif|max:2048',
            'site_favicon_file' => 'nullable|file|mimes:ico,png,svg,webp,jpg,jpeg|max:1024',
            'hero_image_file' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:5120',
            'site_icon' => 'nullable|string|max:500',
            'site_favicon' => 'nullable|string|max:500',
        ]);

        $keys = [
            'site_name', 'site_tagline', 'contact_email',
            'site_icon', 'site_favicon',
            'hero_title', 'hero_subtitle', 'hero_image',
            'social_instagram', 'social_facebook', 'social_youtube', 'social_tiktok',
            'gdpr_notice_text',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::setValue($key, $request->input($key) ?? '', 'general');
            }
        }

        if ($request->boolean('remove_site_icon')) {
            Setting::setValue('site_icon', '', 'general');
        }

        if ($request->boolean('remove_site_favicon')) {
            Setting::setValue('site_favicon', '', 'general');
        }

        if ($request->hasFile('site_icon_file')) {
            $path = '/storage/' . $request->file('site_icon_file')->store('uploads/branding', 'public');
            Setting::setValue('site_icon', $path, 'general');
        }

        if ($request->hasFile('site_favicon_file')) {
            $path = '/storage/' . $request->file('site_favicon_file')->store('uploads/branding', 'public');
            Setting::setValue('site_favicon', $path, 'general');
        }

        if ($request->hasFile('hero_image_file')) {
            $path = '/storage/' . $request->file('hero_image_file')->store('uploads/hero', 'public');
            Setting::setValue('hero_image', $path, 'general');
        }

        return redirect()->route('admin.settings.index', ['tab' => 'general'])->with('success', 'General settings updated.');
    }

    public function updateEmail(Request $request)
    {
        $keys = [
            'mail_driver', 'mail_from_address', 'mail_from_name',
            'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password',
            'brevo_smtp_login', 'brevo_api_key',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::setValue($key, $request->input($key), 'email');
            }
        }

        return redirect()->route('admin.settings.index', ['tab' => 'email'])->with('success', 'Email (SMTP / Brevo) settings saved.');
    }

    public function testEmail(Request $request, DynamicMailService $mailService)
    {
        $data = $request->validate(['test_email' => 'required|email']);
        $res = $mailService->sendEmail(
            $data['test_email'],
            'Admin Tester',
            'Chefchaouen.info Email Configuration Test',
            '<p>Your Chefchaouen.info email settings are working properly.</p>'
        );

        return redirect()->route('admin.settings.index', ['tab' => 'email'])
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    public function updateSeo(Request $request)
    {
        $keys = [
            'seo_meta_title', 'seo_meta_description', 'seo_meta_keywords',
            'seo_og_image', 'seo_robots', 'seo_canonical_base', 'seo_google_verification',
            'code_head_injection', 'code_body_injection',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::setValue($key, $request->input($key), 'seo');
            }
        }

        return redirect()->route('admin.settings.index', ['tab' => 'seo'])->with('success', 'Full SEO & Code Injection settings saved.');
    }

    public function saveAiProvider(Request $request, AiProvider $provider)
    {
        $data = $request->validate([
            'base_url' => 'nullable|string|max:255',
            'api_key' => 'nullable|string|max:500',
            'selected_model' => 'nullable|string|max:160',
            'is_active' => 'nullable|boolean',
        ]);

        $update = [
            'base_url' => $data['base_url'] ?: $provider->base_url,
            'selected_model' => $data['selected_model'] ?: $provider->selected_model,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('api_key')) {
            $update['api_key'] = $data['api_key'];
        }

        $provider->update($update);

        if ($request->boolean('set_default')) {
            AiProvider::where('id', '!=', $provider->id)->update(['is_default' => false]);
            $provider->update(['is_default' => true, 'is_active' => true]);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'ai'])
            ->with('success', $provider->name . ' configuration updated.');
    }

    public function storeCustomAiProvider(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'base_url' => 'required|url|max:255',
            'api_key' => 'nullable|string|max:500',
            'selected_model' => 'nullable|string|max:160',
        ]);

        AiProvider::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(3)),
            'base_url' => $data['base_url'],
            'api_key' => $data['api_key'] ?? null,
            'selected_model' => $data['selected_model'] ?? 'gpt-4o-mini',
            'available_models' => $data['selected_model'] ? [$data['selected_model']] : [],
            'is_active' => true,
            'is_default' => false,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'ai'])->with('success', 'Custom AI provider added.');
    }

    public function setDefaultAiProvider(AiProvider $provider)
    {
        AiProvider::query()->update(['is_default' => false]);
        $provider->update(['is_default' => true, 'is_active' => true]);

        return redirect()->route('admin.settings.index', ['tab' => 'ai'])
            ->with('success', $provider->name . ' (' . ($provider->selected_model ?: 'no model') . ') is now the default model for Chefchaouen Guide Chatbot.');
    }

    public function fetchAiModels(Request $request, AiProvider $provider, AiService $aiService)
    {
        if ($request->filled('api_key')) {
            $provider->update(['api_key' => $request->input('api_key')]);
        }
        if ($request->filled('base_url')) {
            $provider->update(['base_url' => $request->input('base_url')]);
        }

        $res = $aiService->fetchModels($provider->fresh());

        if ($request->expectsJson()) {
            return response()->json($res);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'ai'])
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    public function testAiProvider(Request $request, AiProvider $provider, AiService $aiService)
    {
        if ($request->filled('api_key')) {
            $provider->update(['api_key' => $request->input('api_key')]);
        }
        if ($request->filled('selected_model')) {
            $provider->update(['selected_model' => $request->input('selected_model')]);
        }

        $res = $aiService->testProvider($provider->fresh());

        if ($request->expectsJson()) {
            return response()->json($res);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'ai'])
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:80',
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:2000',
            'show_on_home' => 'nullable|boolean',
        ]);

        Faq::create([
            'category' => $data['category'],
            'question' => $data['question'],
            'answer' => $data['answer'],
            'show_on_home' => $request->boolean('show_on_home', true),
            'sort_order' => (Faq::max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'faqs'])->with('success', 'FAQ added.');
    }

    public function destroyFaq(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.settings.index', ['tab' => 'faqs'])->with('success', 'FAQ deleted.');
    }
}
