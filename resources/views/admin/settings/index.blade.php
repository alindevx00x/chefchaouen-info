@extends('layouts.admin')

@section('header', 'Settings - General, Email, SEO, AI Models & FAQs')

@section('content')
    <!-- Settings Tabs -->
    <div class="flex flex-wrap gap-2 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
        <a href="{{ route('admin.settings.index', ['tab' => 'general']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ $tab === 'general' ? 'bg-chaouen-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">⚙️ General Settings</a>
        <a href="{{ route('admin.settings.index', ['tab' => 'email']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ $tab === 'email' ? 'bg-chaouen-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">✉️ Email (SMTP / Brevo)</a>
        <a href="{{ route('admin.settings.index', ['tab' => 'seo']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ $tab === 'seo' ? 'bg-chaouen-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">🔍 Full SEO & Code Injection</a>
        <a href="{{ route('admin.settings.index', ['tab' => 'ai']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ $tab === 'ai' ? 'bg-chaouen-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">🤖 AI Models & Chatbot</a>
        <a href="{{ route('admin.settings.index', ['tab' => 'faqs']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ $tab === 'faqs' ? 'bg-chaouen-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">❓ FAQs</a>
    </div>

    <!-- TAB 1: GENERAL SETTINGS -->
    @if($tab === 'general')
        <form action="{{ route('admin.settings.general') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
            @csrf
            <h2 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-3">General Platform & Branding Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Site Name</label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Chefchaouen.info' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Site Tagline</label>
                    <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Official Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'contact@chefchaouen.info' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
            </div>

            <!-- Website Icon & Favicon (2 Columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Website Icon (Header &amp; Footer Logo)</label>
                    <div class="flex items-center gap-3 mb-2.5">
                        @if(!empty($settings['site_icon']))
                            <img src="{{ asset($settings['site_icon']) }}" alt="Website Icon" class="w-10 h-10 rounded-lg object-contain border border-slate-200 bg-white p-0.5 shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-t-full rounded-b-lg bg-gradient-to-b from-chaouen-600 to-chaouen-800 flex items-center justify-center text-white shadow-sm shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-3.5 0-6 2.8-6 6.5V21h12V9.5C18 5.8 15.5 3 12 3z"></path>
                                    <circle cx="12" cy="11" r="1.5" fill="currentColor"></circle>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0 space-y-1.5">
                            <input type="file" name="site_icon_file" accept=".png,.jpg,.jpeg,.webp,.svg,.gif" class="block w-full text-xs text-slate-600">
                            <input type="text" name="site_icon" value="{{ $settings['site_icon'] ?? '' }}" placeholder="Icon path or URL (empty = default arch SVG)" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-xs">
                        </div>
                    </div>
                    @if(!empty($settings['site_icon']))
                        <label class="inline-flex items-center gap-1.5 text-[11px] text-slate-600 cursor-pointer">
                            <input type="checkbox" name="remove_site_icon" value="1" class="rounded border-slate-300">
                            Reset to default arch icon
                        </label>
                    @else
                        <p class="text-[11px] text-slate-500">Recommended: square PNG or SVG (e.g. 128x128px).</p>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Favicon (Browser Tab Icon)</label>
                    <div class="flex items-center gap-3 mb-2.5">
                        @if(!empty($settings['site_favicon']))
                            <img src="{{ asset($settings['site_favicon']) }}" alt="Favicon" class="w-10 h-10 rounded-lg object-contain border border-slate-200 bg-white p-1.5 shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-lg border border-dashed border-slate-300 bg-white flex items-center justify-center text-[10px] font-semibold text-slate-400 shrink-0">
                                ICO
                            </div>
                        @endif
                        <div class="flex-1 min-w-0 space-y-1.5">
                            <input type="file" name="site_favicon_file" accept=".ico,.png,.svg,.webp,.jpg,.jpeg" class="block w-full text-xs text-slate-600">
                            <input type="text" name="site_favicon" value="{{ $settings['site_favicon'] ?? '' }}" placeholder="Favicon path or URL (e.g. /storage/uploads/branding/favicon.png)" class="w-full px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-xs">
                        </div>
                    </div>
                    @if(!empty($settings['site_favicon']))
                        <label class="inline-flex items-center gap-1.5 text-[11px] text-slate-600 cursor-pointer">
                            <input type="checkbox" name="remove_site_favicon" value="1" class="rounded border-slate-300">
                            Remove uploaded favicon
                        </label>
                    @else
                        <p class="text-[11px] text-slate-500">Supports .ico, .png, or .svg (32x32px or 64x64px).</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Headline</label>
                    <input type="text" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Background Photo</label>
                    <div class="flex gap-2">
                        <input type="file" name="hero_image_file" accept="image/*" class="text-xs w-1/2">
                        <input type="text" name="hero_image" value="{{ $settings['hero_image'] ?? '/images/gallery/hero-medina.jpg' }}" class="w-1/2 px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Hero Subtitle</label>
                <textarea name="hero_subtitle" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Instagram URL</label>
                    <input type="text" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Facebook URL</label>
                    <input type="text" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">YouTube URL</label>
                    <input type="text" name="social_youtube" value="{{ $settings['social_youtube'] ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">TikTok URL</label>
                    <input type="text" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">GDPR Banner Notice Text</label>
                <input type="text" name="gdpr_notice_text" value="{{ $settings['gdpr_notice_text'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">Save General Settings</button>
        </form>
    @endif

    <!-- TAB 2: EMAIL SETTINGS (Custom SMTP & Brevo) -->
    @if($tab === 'email')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <form action="{{ route('admin.settings.email') }}" method="POST" class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
                @csrf
                <h2 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-3">Email Delivery Settings (Custom SMTP / Brevo)</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Mail Provider Driver</label>
                        <select name="mail_driver" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm">
                            <option value="smtp" @selected(($settings['mail_driver'] ?? '') === 'smtp')>Custom SMTP</option>
                            <option value="brevo_api" @selected(($settings['mail_driver'] ?? '') === 'brevo_api')>Brevo Transactional API</option>
                            <option value="brevo_smtp" @selected(($settings['mail_driver'] ?? '') === 'brevo_smtp')>Brevo SMTP Relay</option>
                            <option value="log" @selected(($settings['mail_driver'] ?? 'log') === 'log')>Local Log (Dev)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">From Address</label>
                        <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? 'contact@chefchaouen.info' }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">From Name</label>
                        <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? 'Chefchaouen.info' }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Custom SMTP Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? '' }}" placeholder="SMTP Host (e.g. smtp.gmail.com)" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                        <input type="text" name="smtp_port" value="{{ $settings['smtp_port'] ?? '587' }}" placeholder="Port (587 / 465)" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                        <select name="smtp_encryption" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="tls" @selected(($settings['smtp_encryption'] ?? '') === 'tls')>TLS</option>
                            <option value="ssl" @selected(($settings['smtp_encryption'] ?? '') === 'ssl')>SSL</option>
                            <option value="" @selected(($settings['smtp_encryption'] ?? '') === '')>None</option>
                        </select>
                        <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}" placeholder="SMTP Username" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white md:col-span-2">
                        <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" placeholder="SMTP Password" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-sky-50/70 border border-sky-200 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-sky-800">Brevo Configuration (API or SMTP Relay)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" name="brevo_smtp_login" value="{{ $settings['brevo_smtp_login'] ?? '' }}" placeholder="Brevo SMTP Login (if using Brevo SMTP)" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                        <input type="password" name="brevo_api_key" value="{{ $settings['brevo_api_key'] ?? '' }}" placeholder="Brevo API Key / SMTP Key (xkeysib-...)" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                    </div>
                </div>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">Save Email Settings</button>
            </form>

            <form action="{{ route('admin.settings.email.test') }}" method="POST" class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-6 h-fit space-y-4">
                @csrf
                <h3 class="font-bold text-sm text-slate-900">Send Test Email</h3>
                <p class="text-xs text-slate-500">Verify your active SMTP or Brevo delivery settings immediately.</p>
                <input type="email" name="test_email" required placeholder="recipient@example.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Run Email Test</button>
            </form>
        </div>
    @endif

    <!-- TAB 3: FULL SEO & CODE INJECTION -->
    @if($tab === 'seo')
        <form action="{{ route('admin.settings.seo') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
            @csrf
            <h2 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-3">Full SEO Settings & Custom Code Injections</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Global Meta Title</label>
                    <input type="text" name="seo_meta_title" value="{{ $settings['seo_meta_title'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Robots Directive</label>
                    <input type="text" name="seo_robots" value="{{ $settings['seo_robots'] ?? 'index, follow' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Global Meta Description</label>
                <textarea name="seo_meta_description" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm">{{ $settings['seo_meta_description'] ?? '' }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Meta Keywords</label>
                    <input type="text" name="seo_meta_keywords" value="{{ $settings['seo_meta_keywords'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">OpenGraph Share Image URL</label>
                    <input type="text" name="seo_og_image" value="{{ $settings['seo_og_image'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Google Search Console Verification Code</label>
                    <input type="text" name="seo_google_verification" value="{{ $settings['seo_google_verification'] ?? '' }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Custom &lt;head&gt; Code Injection (Analytics, Pixel, Schema)</label>
                    <textarea name="code_head_injection" rows="5" placeholder="<!-- Google Analytics / Tag Manager / Custom CSS -->" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono">{{ $settings['code_head_injection'] ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Custom &lt;body&gt; Code Injection (Scripts after body open)</label>
                    <textarea name="code_body_injection" rows="5" placeholder="<!-- Tag Manager noscript / Custom scripts -->" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono">{{ $settings['code_body_injection'] ?? '' }}</textarea>
                </div>
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-bold">Save SEO & Injections</button>
        </form>
    @endif

    <!-- TAB 4: AI MODELS (Groq, OpenAI, Gemini, DeepSeek, OpenRouter + Fetch + Test + Default) -->
    @if($tab === 'ai')
        <div class="space-y-6">
            <div class="bg-chaouen-900 text-white rounded-2xl p-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="font-bold text-base">Chefchaouen Guide Chatbot AI Engine</h2>
                    <p class="text-xs text-chaouen-100 mt-0.5">Configure API keys, fetch live available models per provider, test connectivity, and select the active Default Model powering the public Chefchaouen Guide Chatbot.</p>
                </div>
                @php $activeDefault = $aiProviders->firstWhere('is_default', true); @endphp
                <div class="px-4 py-2 rounded-xl bg-white/10 border border-white/20 text-xs">
                    Default Active: <strong>{{ $activeDefault?->name ?? 'None' }}</strong> ({{ $activeDefault?->selected_model ?? 'local fallback' }})
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach($aiProviders as $provider)
                    <div class="bg-white rounded-2xl border {{ $provider->is_default ? 'border-chaouen-500 ring-2 ring-chaouen-100' : 'border-slate-200' }} p-6 space-y-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-base text-slate-900">{{ $provider->name }}</h3>
                                    @if($provider->is_default)
                                        <span class="px-2.5 py-0.5 rounded-full bg-chaouen-600 text-white text-[10px] font-bold uppercase">Default Chatbot Model</span>
                                    @endif
                                </div>
                                <p class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $provider->base_url }}</p>
                            </div>
                            @if($provider->last_test_status)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $provider->last_test_status === 'success' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $provider->last_test_status === 'success' ? '✓ Tested OK' : '✕ Test Failed' }}
                                </span>
                            @endif
                        </div>

                        <form action="{{ route('admin.settings.ai.save', $provider) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">API Base URL</label>
                                <input type="text" name="base_url" value="{{ $provider->base_url }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">API Key {{ $provider->api_key ? '(Stored - enter new to replace)' : '(Not set)' }}</label>
                                <input type="password" name="api_key" placeholder="{{ $provider->api_key ? '••••••••••••' . substr($provider->api_key, -4) : 'Paste ' . $provider->name . ' API key...' }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Selected Model ({{ count($provider->available_models ?? []) }} available)</label>
                                <select name="selected_model" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-medium">
                                    @foreach(($provider->available_models ?? []) as $m)
                                        <option value="{{ $m }}" @selected($provider->selected_model === $m)>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex flex-wrap gap-2 pt-1">
                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold">Save Key & Model</button>
                                <button type="submit" formaction="{{ route('admin.settings.ai.fetch', $provider) }}" class="px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 text-xs font-semibold">↻ Fetch Models</button>
                                <button type="submit" formaction="{{ route('admin.settings.ai.test', $provider) }}" class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-semibold">⚡ Test Model</button>
                                @if(!$provider->is_default)
                                    <button type="submit" formaction="{{ route('admin.settings.ai.default', $provider) }}" class="px-3.5 py-2 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-xs font-semibold">Set as Default</button>
                                @endif
                            </div>
                        </form>

                        @if($provider->last_test_message)
                            <div class="p-2.5 rounded-xl text-[11px] {{ $provider->last_test_status === 'success' ? 'bg-emerald-50 text-emerald-800' : 'bg-rose-50 text-rose-800' }}">
                                {{ $provider->last_test_message }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Add Custom OpenAI-Compatible Provider -->
            <form action="{{ route('admin.settings.ai.custom') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6">
                @csrf
                <h3 class="font-bold text-sm text-slate-900 mb-3">+ Add Custom AI Provider (OpenAI-Compatible Endpoint)</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="text" name="name" required placeholder="Provider Name (e.g. Mistral, Together)" class="px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    <input type="url" name="base_url" required placeholder="https://api.example.com/v1" class="px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    <input type="password" name="api_key" placeholder="API Key" class="px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    <div class="flex gap-2">
                        <input type="text" name="selected_model" placeholder="Model ID" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-chaouen-600 text-white text-xs font-bold">Add</button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 5: FAQs -->
    @if($tab === 'faqs')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <form action="{{ route('admin.settings.faqs.store') }}" method="POST" class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-6 h-fit space-y-3">
                @csrf
                <h3 class="font-bold text-sm text-slate-900">Add FAQ Item</h3>
                <input type="text" name="category" required placeholder="Category (e.g. Planning, Hiking)" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                <input type="text" name="question" required placeholder="Question" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                <textarea name="answer" rows="4" required placeholder="Answer..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="show_on_home" value="1" checked> Show on Homepage</label>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-chaouen-600 text-white text-xs font-bold">Add FAQ</button>
            </form>
            <div class="lg:col-span-8 space-y-3">
                @foreach($faqs as $faq)
                    <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-start justify-between gap-4">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-chaouen-600">{{ $faq->category }}</span>
                            <h4 class="font-bold text-sm text-slate-900">{{ $faq->question }}</h4>
                            <p class="text-xs text-slate-600 mt-1">{{ $faq->answer }}</p>
                        </div>
                        <form action="{{ route('admin.settings.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('Delete FAQ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
