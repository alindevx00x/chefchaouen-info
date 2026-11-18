<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign In | Chefchaouen.info</title>
    @if(!empty($siteSettings['site_favicon']))
        <link rel="icon" href="{{ asset($siteSettings['site_favicon']) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 space-y-6">
        <div class="text-center">
            @if(!empty($siteSettings['site_icon']))
                <img src="{{ asset($siteSettings['site_icon']) }}" alt="Site Icon" class="w-12 h-12 mx-auto rounded-lg object-contain mb-3">
            @else
                <div class="w-12 h-12 mx-auto rounded-t-full rounded-b-lg bg-blue-700 text-white flex items-center justify-center font-bold text-lg mb-3">C</div>
            @endif
            <h1 class="text-2xl font-bold text-slate-900">Chefchaouen.info</h1>
            <p class="text-xs text-slate-500 mt-1">Platform Administrator Sign In</p>
        </div>

        @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Admin Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-blue-600">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-blue-600">
            </div>
            <label class="flex items-center gap-2 text-xs text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Keep me signed in
            </label>
            <button type="submit" class="w-full py-3 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-semibold text-sm transition">
                Sign In to Dashboard
            </button>
        </form>
    </div>
</body>
</html>
