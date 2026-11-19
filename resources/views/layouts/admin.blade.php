<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Control Panel') | Chefchaouen.info</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!empty($siteSettings['site_favicon']))
        <link rel="icon" href="{{ asset($siteSettings['site_favicon']) }}">
        <link rel="shortcut icon" href="{{ asset($siteSettings['site_favicon']) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- TinyMCE Self-Hosted CDN via cdnjs.cloudflare.com (No API Key, No License, No Registration) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
                    colors: {
                        chaouen: { 50: '#eff6ff', 100: '#dbeafe', 500: '#2563eb', 600: '#1d4ed8', 700: '#1e40af', 800: '#1e3a8a', 900: '#172554' }
                    }
                }
            }
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:sticky top-0 left-0 z-40 h-screen w-68 w-64 bg-slate-950 text-slate-300 flex flex-col transition-transform duration-200 shrink-0">
            <div class="px-5 py-5 border-b border-slate-800 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 text-white">
                    @if(!empty($siteSettings['site_icon']))
                        <img src="{{ asset($siteSettings['site_icon']) }}" alt="Site Icon" class="w-8 h-8 rounded-lg object-contain shrink-0">
                    @else
                        <div class="w-8 h-8 rounded-t-full rounded-b-md bg-chaouen-600 flex items-center justify-center font-bold text-sm">C</div>
                    @endif
                    <div>
                        <span class="font-bold text-sm block leading-none">Chefchaouen.info</span>
                        <span class="text-[10px] text-sky-400 uppercase tracking-wider font-semibold">Admin Console</span>
                    </div>
                </a>
                <button type="button" @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">✕</button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-xs font-medium">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900 text-slate-300' }}">
                    <span>📊</span> Dashboard
                </a>

                <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">Directory & Trails</p>
                <a href="{{ route('admin.places.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.places.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>🏛️</span> Places Directory
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.categories.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>🚪</span> Place Categories
                </a>
                <a href="{{ route('admin.routes.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.routes.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>🥾</span> Tourist Routes
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.reviews.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>⭐</span> Reviews Moderation
                </a>

                <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">Content & Media</p>
                <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.pages.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>📄</span> Public Pages (TinyMCE)
                </a>
                <a href="{{ route('admin.gallery.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.gallery.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>🖼️</span> Gallery Photos
                </a>
                <a href="{{ route('admin.blog.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.blog.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>✍️</span> Blog Posts & Categories
                </a>

                <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">Engagement & System</p>
                <a href="{{ route('admin.contacts.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.contacts.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>✉️</span> Contact Submissions
                </a>
                <a href="{{ route('admin.newsletters.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.newsletters.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>📰</span> Newsletters (CSV)
                </a>
                <a href="{{ route('admin.chatbot-logs.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.chatbot-logs.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>🤖</span> AI Chatbot Logs
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.settings.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>⚙️</span> Settings, Email, SEO & AI
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.users.*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>👥</span> Users Management
                </a>
                <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.profile*') ? 'bg-chaouen-600 text-white' : 'hover:bg-slate-900' }}">
                    <span>👤</span> Profile Settings
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800 space-y-2">
                <a href="{{ route('home') }}" target="_blank" class="block w-full text-center py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-semibold text-sky-300 transition">
                    View Public Site ↗
                </a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 rounded-xl bg-rose-950/70 hover:bg-rose-900 text-rose-200 text-xs font-semibold transition">
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-slate-200 px-5 py-3.5 flex items-center justify-between gap-4 sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button type="button" @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg bg-slate-100 text-slate-700">☰</button>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900">@yield('header', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="hidden sm:inline text-slate-500">Signed in as <strong class="text-slate-800">{{ auth()->user()->name }}</strong></span>
                    <a href="{{ route('admin.profile') }}" class="px-3 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 font-semibold text-slate-700">Profile</a>
                </div>
            </header>

            <main class="flex-1 p-5 sm:p-8 max-w-7xl w-full mx-auto space-y-6">
                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                        ✓ {{ session('success') }}
                    </div>
                @endif
                @if(session('warning'))
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium">
                        ⚠ {{ session('warning') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium">
                        ✕ {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                        @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof tinymce !== 'undefined' && document.querySelector('.tinymce-editor')) {
                tinymce.init({
                    selector: 'textarea.tinymce-editor',
                    height: 400,
                    menubar: true,
                    promotion: false,
                    branding: false,
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
                    toolbar: 'undo redo | blocks | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | removeformat code fullscreen',
                    content_style: 'body { font-family: "Plus Jakarta Sans", sans-serif; font-size: 14px; color: #1e293b; line-height: 1.6; }'
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
