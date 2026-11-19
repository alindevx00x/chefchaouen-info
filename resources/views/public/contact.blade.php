@extends('layouts.public')

@section('title', 'Contact Chefchaouen.info | Blue Pearl Directory')

@section('content')
    <section class="bg-chaouen-900 text-white py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold uppercase tracking-widest text-sky-300">Get in Touch</span>
            <h1 class="font-editorial text-4xl sm:text-5xl font-bold mt-2">Contact Chefchaouen.info</h1>
            <p class="text-chaouen-100 mt-2 text-sm sm:text-base">Questions about visiting Chefchaouen, partner verifications, or updating a directory listing? Write directly to our team.</p>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <div class="md:col-span-4 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-chaouen-600">Official Email</span>
                    <p class="font-bold text-slate-900 mt-1 break-all">{{ $siteSettings['contact_email'] ?? 'contact@chefchaouen.info' }}</p>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">We respond to traveler inquiries, partner verification requests, and editorial suggestions via email.</p>
                </div>
                <div class="bg-chaouen-50 rounded-3xl border border-chaouen-200 p-6">
                    <h3 class="font-bold text-sm text-chaouen-900 mb-1">Suggest a Local Gem</h3>
                    <p class="text-xs text-slate-600 mb-3">Want to submit a place or route directly into our moderation queue?</p>
                    <a href="{{ route('suggest.form') }}" class="text-xs font-bold text-chaouen-700 hover:underline">Go to Suggestion Form →</a>
                </div>
            </div>

            <div class="md:col-span-8 bg-white rounded-3xl border border-slate-200 p-7 sm:p-8 shadow-sm">
                @if(session('success'))
                    <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-5 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1">
                        @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    @include('public.partials.honeypot')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Inquiry Type</label>
                            <select name="inquiry_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:border-chaouen-500">
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Partner Verification">Partner Verification</option>
                                <option value="Route Feedback">Route Feedback</option>
                                <option value="Press & Media">Press & Media</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Subject *</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Message *</label>
                        <textarea name="message" rows="5" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-chaouen-500">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="px-7 py-3 rounded-xl bg-chaouen-600 hover:bg-chaouen-700 text-white text-sm font-semibold transition">
                        Send Message →
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
