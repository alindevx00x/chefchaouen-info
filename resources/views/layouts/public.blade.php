<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $resolvedTitle = html_entity_decode(trim($__env->yieldContent('title')), ENT_QUOTES, 'UTF-8') ?: ($siteSettings['seo_meta_title'] ?? 'Chefchaouen.info | Deeper Than Blue');
        $resolvedDescription = html_entity_decode(trim($__env->yieldContent('meta_description')), ENT_QUOTES, 'UTF-8') ?: ($siteSettings['seo_meta_description'] ?? 'Curated travel directory, interactive map, Rif Mountain hiking routes, and AI travel guide for Chefchaouen, Morocco.');
        $resolvedKeywords = html_entity_decode(trim($__env->yieldContent('meta_keywords')), ENT_QUOTES, 'UTF-8') ?: ($siteSettings['seo_meta_keywords'] ?? 'Chefchaouen, Blue Pearl, Akchour Waterfalls, Rif Mountains, Chefchaouen Routes, Chefchaouen Medina, Hotels, Restaurants, Interactive Map');
        $resolvedCanonical = trim($__env->yieldContent('canonical')) ?: url()->current();
        $resolvedOgImage = trim($__env->yieldContent('og_image')) ?: asset($siteSettings['seo_og_image'] ?? '/images/gallery/hero-medina.jpg');
        $resolvedOgType = trim($__env->yieldContent('og_type')) ?: 'website';
        $baseRobots = trim((string) ($siteSettings['seo_robots'] ?? 'index, follow'));
        if (!str_contains(strtolower($baseRobots), 'noindex') && !str_contains(strtolower($baseRobots), 'max-image-preview')) {
            $baseRobots .= ', max-image-preview:large, max-snippet:-1, max-video-preview:-1';
        }
        $siteDisplayName = $siteSettings['site_name'] ?? 'Chefchaouen.info';
        $sameAsLinks = array_values(array_filter([
            $siteSettings['social_instagram'] ?? null,
            $siteSettings['social_facebook'] ?? null,
            $siteSettings['social_youtube'] ?? null,
            $siteSettings['social_tiktok'] ?? null,
        ]));
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => url('/') . '#organization',
                    'name' => $siteDisplayName,
                    'url' => url('/'),
                    'email' => $siteSettings['contact_email'] ?? 'contact@chefchaouen.info',
                    'logo' => !empty($siteSettings['site_icon']) ? asset($siteSettings['site_icon']) : asset('/images/gallery/hero-medina.jpg'),
                    'sameAs' => $sameAsLinks,
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => url('/') . '#website',
                    'url' => url('/'),
                    'name' => $siteDisplayName,
                    'description' => $siteSettings['seo_meta_description'] ?? 'Curated travel directory for Chefchaouen, Morocco.',
                    'publisher' => ['@id' => url('/') . '#organization'],
                    'inLanguage' => ['en', 'fr', 'es', 'ar'],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/places') . '?q={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ];
    @endphp
    <title>{{ $resolvedTitle }}</title>
    <meta name="description" content="{{ $resolvedDescription }}">
    <meta name="keywords" content="{{ $resolvedKeywords }}">
    <meta name="robots" content="@yield('meta_robots', $baseRobots)">
    <meta name="author" content="@yield('meta_author', $siteDisplayName)">
    <link rel="canonical" href="{{ $resolvedCanonical }}">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}">
    <link rel="alternate" type="text/plain" title="LLMs Guide" href="{{ route('llms.txt') }}">

    <!-- Geographic Metadata (GEO / Local Search) -->
    <meta name="geo.region" content="MA-01">
    <meta name="geo.placename" content="@yield('geo_placename', 'Chefchaouen, Rif Mountains, Morocco')">
    <meta name="geo.position" content="@yield('geo_position', '35.1688;-5.2636')">
    <meta name="ICBM" content="@yield('geo_icbm', '35.1688, -5.2636')">

    @if(!empty($siteSettings['seo_google_verification']))
        <meta name="google-site-verification" content="{{ $siteSettings['seo_google_verification'] }}">
    @endif

    <!-- OpenGraph & Twitter Cards -->
    <meta property="og:site_name" content="{{ $siteDisplayName }}">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="{{ $resolvedCanonical }}">
    <meta property="og:title" content="{{ $resolvedTitle }}">
    <meta property="og:description" content="{{ $resolvedDescription }}">
    <meta property="og:image" content="{{ $resolvedOgImage }}">
    <meta property="og:image:alt" content="{{ $resolvedTitle }}">
    <meta property="og:type" content="{{ $resolvedOgType }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $resolvedTitle }}">
    <meta name="twitter:description" content="{{ $resolvedDescription }}">
    <meta name="twitter:image" content="{{ $resolvedOgImage }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!empty($siteSettings['site_favicon']))
        <link rel="icon" href="{{ asset($siteSettings['site_favicon']) }}">
        <link rel="shortcut icon" href="{{ asset($siteSettings['site_favicon']) }}">
        <link rel="apple-touch-icon" href="{{ asset($siteSettings['site_favicon']) }}">
    @endif

    <script type="application/ld+json">{!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @stack('schema')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        serif: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                    },
                    colors: {
                        chaouen: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            800: '#1e3a8a',
                            900: '#172554',
                        },
                        skywash: '#f7faff',
                        terracotta: '#d97706',
                    }
                }
            }
        }
    </script>
    <style>
        .font-editorial { font-family: 'Cormorant Garamond', Georgia, serif; }
        /* Hide Google Translate top banner cleanly */
        .goog-te-banner-frame.skiptranslate, body > .skiptranslate { display: none !important; }
        body { top: 0px !important; }
        .goog-logo-link { display: none !important; }
        .goog-te-gadget { font-size: 0 !important; }
        .goog-te-gadget .goog-te-combo {
            font-size: 13px !important;
            padding: 6px 10px;
            border-radius: 9999px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            outline: none;
        }
        .arch-door {
            border-top-left-radius: 999px;
            border-top-right-radius: 999px;
            border-bottom-left-radius: 14px;
            border-bottom-right-radius: 14px;
        }
        /* Rich HTML typography for Public Pages, Blog Posts, Place & Route Descriptions */
        .prose {
            color: #334155;
            font-size: 1rem;
            line-height: 1.8;
        }
        .prose > :first-child { margin-top: 0 !important; }
        .prose > :last-child { margin-bottom: 0 !important; }
        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            color: #0f172a;
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -0.01em;
        }
        .prose h1 { font-size: 2.25rem; margin-top: 2rem; margin-bottom: 1rem; }
        .prose h2 { font-size: 1.875rem; margin-top: 2rem; margin-bottom: 0.875rem; padding-bottom: 0.45rem; border-bottom: 1px solid #e2e8f0; color: #172554; }
        .prose h3 { font-size: 1.45rem; margin-top: 1.6rem; margin-bottom: 0.65rem; color: #1e3a8a; }
        .prose h4 { font-size: 1.2rem; margin-top: 1.35rem; margin-bottom: 0.5rem; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #1e40af; }
        .prose h5 { font-size: 1.05rem; margin-top: 1.15rem; margin-bottom: 0.4rem; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .prose h6 { font-size: 0.95rem; margin-top: 1rem; margin-bottom: 0.35rem; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; }
        .prose p { margin-top: 0.85rem; margin-bottom: 1rem; }
        .prose strong, .prose b { font-weight: 700; color: #0f172a; }
        .prose em, .prose i { font-style: italic; }
        .prose u { text-decoration: underline; text-underline-offset: 3px; }
        .prose a { color: #1d4ed8; font-weight: 600; text-decoration: underline; text-underline-offset: 2px; }
        .prose a:hover { color: #1e3a8a; }
        .prose ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-top: 0.85rem; margin-bottom: 1.15rem; }
        .prose ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-top: 0.85rem; margin-bottom: 1.15rem; }
        .prose li { margin-top: 0.4rem; margin-bottom: 0.4rem; }
        .prose li::marker { color: #1d4ed8; font-weight: 700; }
        .prose blockquote {
            margin: 1.5rem 0;
            padding: 1rem 1.25rem;
            border-left: 4px solid #1d4ed8;
            background: #eff6ff;
            border-radius: 0 1rem 1rem 0;
            color: #1e3a8a;
            font-style: italic;
        }
        .prose hr { border: 0; border-top: 1px solid #e2e8f0; margin: 2rem 0; }
        .prose table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; font-size: 0.9rem; overflow: hidden; border-radius: 0.75rem; border: 1px solid #e2e8f0; }
        .prose thead { background: #eff6ff; }
        .prose th { text-align: left; font-weight: 700; color: #1e3a8a; padding: 0.75rem 1rem; border-bottom: 1px solid #bfdbfe; }
        .prose td { padding: 0.7rem 1rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .prose img { border-radius: 1rem; margin: 1.25rem 0; max-width: 100%; height: auto; }
        .prose code { background: #f1f5f9; color: #0f172a; padding: 0.15rem 0.4rem; border-radius: 0.375rem; font-size: 0.875em; }
        [x-cloak] { display: none !important; }
    </style>

    {!! $siteSettings['code_head_injection'] ?? '' !!}
    @stack('head')
</head>
<body class="bg-skywash text-slate-800 font-sans antialiased selection:bg-chaouen-100 selection:text-chaouen-900"
      x-data="{
        mobileMenu: false,
        chatOpen: sessionStorage.getItem('chefchaouen_chat_open_v1') === '1',
        gdprAccepted: localStorage.getItem('chefchaouen_gdpr_ok') === '1',
        showTopBtn: false
      }"
      x-init="$watch('chatOpen', v => { try { sessionStorage.setItem('chefchaouen_chat_open_v1', v ? '1' : '0'); } catch (e) {} if (v) { $nextTick(() => { const box = document.getElementById('chatbot-messages'); if (box) box.scrollTop = box.scrollHeight; }); } })"
      @scroll.window="showTopBtn = window.scrollY > 450"
      @open-chaouen-chat.window="chatOpen = true; $nextTick(() => { if ($event.detail && $event.detail.prompt) { window.askChefchaouenChatbot($event.detail.prompt); } })">

    {!! $siteSettings['code_body_injection'] ?? '' !!}

    <!-- Subtle Top Announcement / Language Strip -->
    <div class="bg-chaouen-900 text-white text-xs py-2 border-b border-chaouen-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-3 text-chaouen-100">
                <span class="inline-flex items-center gap-1.5 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                    Chefchaouen Deeper Than Blue
                </span>
                <span class="hidden sm:inline text-chaouen-300">|</span>
                <a href="mailto:{{ $siteSettings['contact_email'] ?? 'contact@chefchaouen.info' }}" class="hidden sm:inline hover:text-white transition">
                    {{ $siteSettings['contact_email'] ?? 'contact@chefchaouen.info' }}
                </a>
            </div>
            <div class="flex items-center gap-2.5">
                <!-- Flag-Only Language Switcher + Google Translate Engine -->
                <div class="flex items-center gap-1.5 bg-chaouen-800/80 rounded-full p-1 border border-chaouen-700" translate="no">
                    <button type="button" onclick="setSiteLanguage('en')" title="English" aria-label="English"
                            class="w-6 h-4 rounded-sm overflow-hidden hover:ring-2 hover:ring-white/80 transition focus:outline-none">
                        <svg viewBox="0 0 60 36" class="w-full h-full block" aria-hidden="true">
                            <clipPath id="uk-clip"><rect width="60" height="36" rx="2"/></clipPath>
                            <g clip-path="url(#uk-clip)">
                                <rect width="60" height="36" fill="#012169"/>
                                <path d="M0,0 L60,36 M60,0 L0,36" stroke="#fff" stroke-width="7"/>
                                <path d="M0,0 L60,36 M60,0 L0,36" stroke="#C8102E" stroke-width="4"/>
                                <path d="M30,0 V36 M0,18 H60" stroke="#fff" stroke-width="11"/>
                                <path d="M30,0 V36 M0,18 H60" stroke="#C8102E" stroke-width="6.5"/>
                            </g>
                        </svg>
                    </button>
                    <button type="button" onclick="setSiteLanguage('es')" title="Español" aria-label="Spanish"
                            class="w-6 h-4 rounded-sm overflow-hidden hover:ring-2 hover:ring-white/80 transition focus:outline-none">
                        <svg viewBox="0 0 60 36" class="w-full h-full block" aria-hidden="true">
                            <rect width="60" height="9" y="0" fill="#AA151B"/>
                            <rect width="60" height="18" y="9" fill="#F1BF00"/>
                            <rect width="60" height="9" y="27" fill="#AA151B"/>
                        </svg>
                    </button>
                    <button type="button" onclick="setSiteLanguage('fr')" title="Français" aria-label="French"
                            class="w-6 h-4 rounded-sm overflow-hidden hover:ring-2 hover:ring-white/80 transition focus:outline-none">
                        <svg viewBox="0 0 60 36" class="w-full h-full block" aria-hidden="true">
                            <rect width="20" height="36" x="0" fill="#002395"/>
                            <rect width="20" height="36" x="20" fill="#FFFFFF"/>
                            <rect width="20" height="36" x="40" fill="#ED2939"/>
                        </svg>
                    </button>
                    <button type="button" onclick="setSiteLanguage('ar')" title="العربية" aria-label="Arabic"
                            class="w-6 h-4 rounded-sm overflow-hidden hover:ring-2 hover:ring-white/80 transition focus:outline-none">
                        <svg viewBox="0 0 60 36" class="w-full h-full block" aria-hidden="true">
                            <rect width="60" height="36" fill="#C1272D"/>
                            <polygon points="30,9 32.3,16.1 39.8,16.1 33.7,20.5 36,27.6 30,23.2 24,27.6 26.3,20.5 20.2,16.1 27.7,16.1"
                                     fill="none" stroke="#006233" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <div id="google_translate_element" class="hidden"></div>
            </div>
        </div>
    </div>

    <!-- Main Editorial Navigation -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 sm:py-3 flex items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3.5 group shrink-0">
                @if(!empty($siteSettings['site_icon']))
                    <img src="{{ asset($siteSettings['site_icon']) }}"
                         alt="{{ $siteSettings['site_name'] ?? 'Chefchaouen.info' }}"
                         class="h-14 sm:h-16 w-auto max-w-[200px] rounded-xl object-contain shrink-0 group-hover:scale-105 transition">
                @else
                    <div class="w-12 h-12 rounded-t-full rounded-b-lg bg-gradient-to-b from-chaouen-600 to-chaouen-800 flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-3.5 0-6 2.8-6 6.5V21h12V9.5C18 5.8 15.5 3 12 3z" />
                            <circle cx="12" cy="11" r="1.5" fill="currentColor" />
                        </svg>
                    </div>
                @endif
                <div>
                    <span class="block text-lg font-bold tracking-tight text-slate-900 leading-none">{{ $siteSettings['site_name'] ?? 'Chefchaouen.info' }}</span>
                    <span class="block text-[11px] tracking-widest uppercase text-chaouen-600 font-semibold mt-0.5">Blue Pearl Guide</span>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-7 text-sm font-medium text-slate-700">
                <a href="{{ route('home') }}" class="hover:text-chaouen-600 transition {{ request()->routeIs('home') ? 'text-chaouen-600 font-semibold' : '' }}">Home</a>
                <a href="{{ route('places.index') }}" class="hover:text-chaouen-600 transition {{ request()->routeIs('places.*') ? 'text-chaouen-600 font-semibold' : '' }}">Places to Visit</a>
                <a href="{{ route('routes.index') }}" class="hover:text-chaouen-600 transition {{ request()->routeIs('routes.*') ? 'text-chaouen-600 font-semibold' : '' }}">Tourist Routes</a>
                <a href="{{ route('map.index') }}" class="hover:text-chaouen-600 transition {{ request()->routeIs('map.*') ? 'text-chaouen-600 font-semibold' : '' }}">Interactive Map</a>
                <a href="{{ route('page.show', 'history-culture') }}" class="hover:text-chaouen-600 transition {{ request()->is('page/history-culture') ? 'text-chaouen-600 font-semibold' : '' }}">History & Culture</a>
            </nav>

            <div class="flex items-center gap-2.5">
                <button type="button" @click="$dispatch('open-chaouen-chat')"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-chaouen-50 text-chaouen-700 border border-chaouen-200 text-xs font-semibold hover:bg-chaouen-100 transition">
                    <span>✨</span> Ask Guide AI
                </button>
                <a href="{{ route('suggest.form') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-chaouen-600 text-white text-xs sm:text-sm font-semibold hover:bg-chaouen-700 shadow-sm transition">
                    + Suggest Place
                </a>
                <button type="button" @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-lg text-slate-700 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-cloak class="lg:hidden border-t border-slate-100 bg-white px-4 py-4 space-y-2 text-sm font-medium">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg hover:bg-chaouen-50">Home</a>
            <a href="{{ route('places.index') }}" class="block px-3 py-2 rounded-lg hover:bg-chaouen-50">Places to Visit</a>
            <a href="{{ route('routes.index') }}" class="block px-3 py-2 rounded-lg hover:bg-chaouen-50">Tourist Routes</a>
            <a href="{{ route('map.index') }}" class="block px-3 py-2 rounded-lg hover:bg-chaouen-50">Interactive Map</a>
            <a href="{{ route('page.show', 'history-culture') }}" class="block px-3 py-2 rounded-lg hover:bg-chaouen-50">History & Culture</a>
            <a href="{{ route('faq.index') }}" class="block px-3 py-2 rounded-lg hover:bg-chaouen-50">FAQs</a>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Editorial Footer (Strictly No Office Address, No Phone, No Opening Hours) -->
    <footer class="bg-slate-950 text-slate-300 mt-20 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        @if(!empty($siteSettings['site_icon']))
                            <img src="{{ asset($siteSettings['site_icon']) }}"
                                 alt="{{ $siteSettings['site_name'] ?? 'Chefchaouen.info' }}"
                                 class="w-9 h-9 rounded-lg object-contain shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-t-full rounded-b-md bg-chaouen-600 flex items-center justify-center text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-3.5 0-6 2.8-6 6.5V21h12V9.5C18 5.8 15.5 3 12 3z" />
                                </svg>
                            </div>
                        @endif
                        <span class="text-lg font-bold text-white">{{ $siteSettings['site_name'] ?? 'Chefchaouen.info' }}</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed mb-4">
                        Chefchaouen... Deeper Than Blue. Discover. Experience. Remember. Your curated companion to the Blue Pearl of Morocco and the Rif Mountains.
                    </p>
                    <p class="text-sm text-chaouen-300 font-medium">
                        Official Email: <a href="mailto:{{ $siteSettings['contact_email'] ?? 'contact@chefchaouen.info' }}" class="hover:underline text-white">{{ $siteSettings['contact_email'] ?? 'contact@chefchaouen.info' }}</a>
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-4">Explore Directory</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('places.index') }}" class="hover:text-white transition">Places to Visit</a></li>
                        <li><a href="{{ route('routes.index') }}" class="hover:text-white transition">Tourist Routes (Inside & Outside City)</a></li>
                        <li><a href="{{ route('map.index') }}" class="hover:text-white transition">Interactive Google Map</a></li>
                        <li><a href="{{ route('gallery.index') }}" class="hover:text-white transition">Photo Gallery</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Travel Journal</a></li>
                        <li><a href="{{ route('suggest.form') }}" class="hover:text-white transition">Suggest a Place or Route</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-4">Information & Legal</h4>
                    <ul class="space-y-2.5 text-sm">
                        @foreach($footerPages as $fp)
                            <li><a href="{{ route('page.show', $fp->slug) }}" class="hover:text-white transition">{{ $fp->title }}</a></li>
                        @endforeach
                        <li><a href="{{ route('faq.index') }}" class="hover:text-white transition">FAQs</a></li>
                        <li><a href="{{ route('contact.index') }}" class="hover:text-white transition">Contact Us</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-4">Follow the Journey</h4>
                    <p class="text-sm text-slate-400 mb-4">Follow Chefchaouen stories, seasonal Rif trail notes, and hidden medina corners.</p>
                    <div class="flex items-center gap-3 mb-6">
                        @if(!empty($siteSettings['social_instagram']))
                            <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-chaouen-600 flex items-center justify-center text-white transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.8A3.95 3.95 0 0 0 3.8 7.75v8.5a3.95 3.95 0 0 0 3.95 3.95h8.5a3.95 3.95 0 0 0 3.95-3.95v-8.5a3.95 3.95 0 0 0-3.95-3.95h-8.5Zm9.15 1.45a1.15 1.15 0 1 1 0 2.3 1.15 1.15 0 0 1 0-2.3ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.8a3.2 3.2 0 1 0 0 6.4 3.2 3.2 0 0 0 0-6.4Z"/>
                                </svg>
                            </a>
                        @endif
                        @if(!empty($siteSettings['social_facebook']))
                            <a href="{{ $siteSettings['social_facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-chaouen-600 flex items-center justify-center text-white transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M13.5 22v-8.2h2.75l.41-3.2H13.5V8.56c0-.93.26-1.56 1.59-1.56h1.7V4.14c-.3-.04-1.31-.14-2.49-.14-2.46 0-4.15 1.5-4.15 4.26v2.34H7.37v3.2h2.78V22h3.35Z"/>
                                </svg>
                            </a>
                        @endif
                        @if(!empty($siteSettings['social_youtube']))
                            <a href="{{ $siteSettings['social_youtube'] }}" target="_blank" rel="noopener" aria-label="YouTube" title="YouTube" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-chaouen-600 flex items-center justify-center text-white transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M21.58 7.19a2.75 2.75 0 0 0-1.94-1.95C17.93 4.8 12 4.8 12 4.8s-5.93 0-7.64.44A2.75 2.75 0 0 0 2.42 7.2 28.9 28.9 0 0 0 2 12a28.9 28.9 0 0 0 .42 4.81 2.75 2.75 0 0 0 1.94 1.95C6.07 19.2 12 19.2 12 19.2s5.93 0 7.64-.44a2.75 2.75 0 0 0 1.94-1.95A28.9 28.9 0 0 0 22 12a28.9 28.9 0 0 0-.42-4.81ZM10 15.1V8.9l5.2 3.1L10 15.1Z"/>
                                </svg>
                            </a>
                        @endif
                        @if(!empty($siteSettings['social_tiktok']))
                            <a href="{{ $siteSettings['social_tiktok'] }}" target="_blank" rel="noopener" aria-label="TikTok" title="TikTok" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-chaouen-600 flex items-center justify-center text-white transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M16.6 5.82A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 1 1-2.59-2.59c.27 0 .53.04.78.12V9.77a5.68 5.68 0 0 0-.78-.05A5.68 5.68 0 1 0 15.54 15.4V9.04a7.33 7.33 0 0 0 4.29 1.38V7.34a4.27 4.27 0 0 1-3.23-1.52Z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                    <button type="button" @click="$dispatch('open-chaouen-chat')" class="w-full py-2.5 px-4 rounded-xl bg-chaouen-600/20 border border-chaouen-500/40 text-chaouen-200 text-sm font-medium hover:bg-chaouen-600 hover:text-white transition">
                        ✨ Ask Chefchaouen Guide AI
                    </button>
                </div>
            </div>

            <div class="border-t border-slate-800/80 mt-12 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'Chefchaouen.info' }}. Made with care for the people who call the Blue City home.</p>
                <p>Official inquiries: {{ $siteSettings['contact_email'] ?? 'contact@chefchaouen.info' }}</p>
            </div>
        </div>
    </footer>

    <!-- Floating Back to Top Button -->
    <button type="button" x-show="showTopBtn" x-transition x-cloak
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            title="Back to top"
            class="fixed bottom-24 right-6 z-40 w-11 h-11 rounded-full bg-white text-chaouen-700 border border-slate-200 shadow-lg hover:bg-chaouen-600 hover:text-white flex items-center justify-center transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

    <!-- Floating Chefchaouen Guide AI Chatbot -->
    <div class="fixed bottom-6 right-6 z-50">
        <!-- Trigger Button -->
        <button type="button" x-show="!chatOpen" @click="chatOpen = true"
                class="flex items-center gap-2.5 px-5 py-3.5 rounded-full bg-gradient-to-r from-chaouen-700 to-sky-600 text-white shadow-xl hover:shadow-2xl hover:scale-105 transition font-semibold text-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Chefchaouen Guide</span>
            <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full">AI</span>
        </button>

        <!-- Chat Panel -->
        <div x-show="chatOpen" x-transition x-cloak
             class="w-[360px] sm:w-[400px] h-[540px] max-h-[82vh] bg-white rounded-3xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden">
            <div class="bg-gradient-to-r from-chaouen-800 to-chaouen-600 px-5 py-4 text-white flex items-center justify-between gap-2">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <h3 class="font-bold text-sm">Chefchaouen Guide</h3>
                    </div>
                    <p class="text-[11px] text-chaouen-100 mt-0.5">Grounded strictly in Chefchaouen.info directory</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="window.clearChefchaouenChat()" title="Start a new conversation"
                            class="px-2.5 py-1 rounded-full bg-white/15 hover:bg-white/25 text-[11px] font-semibold transition">
                        New Chat
                    </button>
                    <button type="button" @click="chatOpen = false" aria-label="Close chat" class="p-1.5 rounded-full hover:bg-white/15 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50 text-sm">
                <div class="bg-white border border-slate-200 rounded-2xl rounded-tl-sm p-3.5 shadow-sm text-slate-700">
                    <p class="font-semibold text-chaouen-800 mb-1">Marhaba! I am your Chefchaouen Guide.</p>
                    <p class="text-xs leading-relaxed text-slate-600">Ask me about medina places, Rif Mountain routes, Akchour waterfalls, local food, or parking. Your chat stays saved while you explore the website.</p>
                </div>

                <!-- Suggested Prompts -->
                <div id="chatbot-suggestions" class="flex flex-wrap gap-1.5 pt-1">
                    <button type="button" onclick="window.askChefchaouenChatbot('What are the best inside-city and Rif hiking routes?')" class="text-left text-xs px-3 py-1.5 rounded-full bg-chaouen-50 text-chaouen-700 border border-chaouen-200 hover:bg-chaouen-100 transition">🥾 Best hiking routes?</button>
                    <button type="button" onclick="window.askChefchaouenChatbot('How do I visit Akchour Waterfalls from Chefchaouen?')" class="text-left text-xs px-3 py-1.5 rounded-full bg-chaouen-50 text-chaouen-700 border border-chaouen-200 hover:bg-chaouen-100 transition">💧 Akchour Waterfalls guide</button>
                    <button type="button" onclick="window.askChefchaouenChatbot('Where are hidden gems and great sunset views in the medina?')" class="text-left text-xs px-3 py-1.5 rounded-full bg-chaouen-50 text-chaouen-700 border border-chaouen-200 hover:bg-chaouen-100 transition">🌅 Sunset & hidden gems</button>
                    <button type="button" onclick="window.askChefchaouenChatbot('Where should I eat traditional Moroccan food or stay in a riad?')" class="text-left text-xs px-3 py-1.5 rounded-full bg-chaouen-50 text-chaouen-700 border border-chaouen-200 hover:bg-chaouen-100 transition">🍽️ Restaurants & Riads</button>
                </div>

                <div id="chatbot-thread" class="space-y-3"></div>
            </div>

            <form onsubmit="event.preventDefault(); window.submitChatbotInput();" class="p-3 bg-white border-t border-slate-200 flex items-center gap-2">
                <input id="chatbot-input" type="text" placeholder="Ask about Chefchaouen places or routes..."
                       class="flex-1 text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-chaouen-500 focus:ring-2 focus:ring-chaouen-100 outline-none">
                <button type="submit" id="chatbot-send-btn"
                        class="px-4 py-2.5 rounded-xl bg-chaouen-600 text-white text-sm font-semibold hover:bg-chaouen-700 transition">
                    Send
                </button>
            </form>
        </div>
    </div>

    <!-- Bottom GDPR Cookie Consent Banner -->
    <div x-show="!gdprAccepted" x-transition x-cloak
         class="fixed bottom-4 left-4 right-4 md:right-auto md:max-w-lg z-40 bg-slate-900 text-white rounded-2xl shadow-2xl border border-slate-700 p-4 sm:p-5">
        <div class="flex flex-col gap-3">
            <div class="text-xs sm:text-sm text-slate-200 leading-relaxed">
                <strong class="text-white block mb-1">Privacy & GDPR Notice</strong>
                {{ $siteSettings['gdpr_notice_text'] ?? 'We use essential cookies and translation preferences to enhance your journey across Chefchaouen.info.' }}
                <div class="mt-1.5 flex flex-wrap gap-3 text-xs">
                    <a href="{{ route('page.show', 'cookie-policy') }}" class="text-sky-400 hover:underline">Cookie Policy</a>
                    <a href="{{ route('page.show', 'privacy-policy') }}" class="text-sky-400 hover:underline">Privacy Policy</a>
                    <a href="{{ route('page.show', 'terms-and-conditions') }}" class="text-sky-400 hover:underline">Terms</a>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2">
                <button type="button" @click="localStorage.setItem('chefchaouen_gdpr_ok', '1'); gdprAccepted = true;"
                        class="px-4 py-2 rounded-xl bg-chaouen-600 hover:bg-chaouen-500 text-white text-xs font-semibold transition">
                    Accept & Continue
                </button>
            </div>
        </div>
    </div>

    <!-- Google Translate Script (EN default -> ES, FR, AR) -->
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,es,fr,ar',
                autoDisplay: false
            }, 'google_translate_element');
        }

        function setSiteLanguage(lang) {
            if (lang === 'en') {
                document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                document.cookie = 'googtrans=/en/en; path=/;';
                document.documentElement.dir = 'ltr';
                window.location.reload();
                return;
            }
            document.cookie = 'googtrans=/en/' + lang + '; path=/;';
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                select.value = lang;
                select.dispatchEvent(new Event('change'));
                document.documentElement.dir = (lang === 'ar') ? 'rtl' : 'ltr';
            } else {
                window.location.reload();
            }
        }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- Chefchaouen Guide Chatbot Script (Persistent across pages via localStorage) -->
    <script>
        (function () {
            const CHAT_STORAGE_KEY = 'chefchaouen_chat_history_v1';
            const MAX_STORED_MESSAGES = 40;
            window.chaouenChatHistory = [];

            function formatChatText(text) {
                return String(text || '')
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\[(.*?)\]\((\/.*?)\)/g, '<a href="$2" class="text-chaouen-600 underline font-medium">$1</a>')
                    .replace(/\n/g, '<br>');
            }

            function loadStoredMessages() {
                try {
                    const raw = localStorage.getItem(CHAT_STORAGE_KEY);
                    if (!raw) return [];
                    const parsed = JSON.parse(raw);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    return [];
                }
            }

            function saveStoredMessages(messages) {
                try {
                    const trimmed = messages.slice(-MAX_STORED_MESSAGES);
                    localStorage.setItem(CHAT_STORAGE_KEY, JSON.stringify(trimmed));
                } catch (e) {
                    // Ignore storage quota errors cleanly
                }
            }

            function appendMessageBubble(entry) {
                const thread = document.getElementById('chatbot-thread');
                if (!thread) return;

                const bubble = document.createElement('div');
                if (entry.role === 'user') {
                    bubble.className = 'flex justify-end';
                    bubble.innerHTML = `<div class="bg-chaouen-600 text-white rounded-2xl rounded-tr-sm px-3.5 py-2.5 max-w-[85%] text-xs sm:text-sm shadow-sm">${formatChatText(entry.content)}</div>`;
                } else {
                    bubble.className = 'flex justify-start';
                    const borderClass = entry.blocked ? 'border-amber-300 bg-amber-50/50' : 'border-slate-200';
                    bubble.innerHTML = `<div class="bg-white border ${borderClass} rounded-2xl rounded-tl-sm px-3.5 py-3 max-w-[90%] text-xs sm:text-sm text-slate-700 shadow-sm leading-relaxed">${formatChatText(entry.content)}</div>`;
                }
                thread.appendChild(bubble);
            }

            function renderRestoredChat() {
                const thread = document.getElementById('chatbot-thread');
                const box = document.getElementById('chatbot-messages');
                const suggestions = document.getElementById('chatbot-suggestions');
                if (!thread || !box) return;

                const saved = loadStoredMessages();
                thread.innerHTML = '';
                window.chaouenChatHistory = [];

                if (saved.length > 0) {
                    if (suggestions) suggestions.classList.add('hidden');
                    saved.forEach(item => {
                        if (item && (item.role === 'user' || item.role === 'assistant') && item.content) {
                            appendMessageBubble(item);
                            window.chaouenChatHistory.push({ role: item.role, content: item.content });
                        }
                    });
                    box.scrollTop = box.scrollHeight;
                } else if (suggestions) {
                    suggestions.classList.remove('hidden');
                }
            }

            window.clearChefchaouenChat = function () {
                try {
                    localStorage.removeItem(CHAT_STORAGE_KEY);
                } catch (e) {}
                window.chaouenChatHistory = [];
                renderRestoredChat();
            };

            window.submitChatbotInput = function () {
                const input = document.getElementById('chatbot-input');
                const text = input.value.trim();
                if (!text) return;
                input.value = '';
                window.askChefchaouenChatbot(text);
            };

            window.askChefchaouenChatbot = async function (promptText) {
                const thread = document.getElementById('chatbot-thread');
                const box = document.getElementById('chatbot-messages');
                const suggestions = document.getElementById('chatbot-suggestions');
                if (!thread || !box) return;

                if (suggestions) suggestions.classList.add('hidden');

                const userEntry = { role: 'user', content: promptText };
                appendMessageBubble(userEntry);

                const stored = loadStoredMessages();
                stored.push(userEntry);
                saveStoredMessages(stored);
                const historyForApi = window.chaouenChatHistory.slice(-10);
                window.chaouenChatHistory.push(userEntry);

                const loadingBubble = document.createElement('div');
                loadingBubble.className = 'flex justify-start';
                loadingBubble.innerHTML = `<div class="bg-white border border-slate-200 rounded-2xl rounded-tl-sm px-3.5 py-2.5 text-xs text-slate-500 shadow-sm">Consulting Chefchaouen.info directory...</div>`;
                thread.appendChild(loadingBubble);
                box.scrollTop = box.scrollHeight;

                try {
                    const res = await fetch('{{ route('chatbot.ask') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            message: promptText,
                            history: historyForApi
                        })
                    });

                    loadingBubble.remove();

                    if (res.status === 429) {
                        const rateEntry = {
                            role: 'assistant',
                            content: 'You are sending messages very quickly. Please wait a few seconds before asking another question.',
                            blocked: true
                        };
                        appendMessageBubble(rateEntry);
                        box.scrollTop = box.scrollHeight;
                        return;
                    }

                    const data = await res.json();
                    const replyText = data.reply || data.message || 'Sorry, please try again.';
                    const botEntry = { role: 'assistant', content: replyText, blocked: Boolean(data.blocked) };
                    appendMessageBubble(botEntry);

                    const updated = loadStoredMessages();
                    updated.push(botEntry);
                    saveStoredMessages(updated);
                    window.chaouenChatHistory.push({ role: 'assistant', content: replyText });
                    box.scrollTop = box.scrollHeight;
                } catch (err) {
                    loadingBubble.innerHTML = `<div class="bg-red-50 border border-red-200 rounded-2xl px-3.5 py-2 text-xs text-red-700">Unable to reach Chefchaouen Guide right now.</div>`;
                }
            };

            // Restore persisted chat immediately and keep multi-tab storage in sync
            renderRestoredChat();
            window.addEventListener('storage', function (e) {
                if (e.key === CHAT_STORAGE_KEY) {
                    renderRestoredChat();
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
