@php
    $rawImages = [];
    if (!empty($featured)) {
        $rawImages[] = $featured;
    }
    if (is_array($album ?? null)) {
        foreach ($album as $photo) {
            if (!empty($photo)) {
                $rawImages[] = $photo;
            }
        }
    }
    $uniqueImages = [];
    foreach ($rawImages as $img) {
        if (!in_array($img, $uniqueImages, true)) {
            $uniqueImages[] = $img;
        }
    }
    $galleryUrls = array_map(fn ($path) => asset($path), $uniqueImages);
@endphp

@if(count($galleryUrls) > 0)
    <div class="space-y-3"
         x-data="{
            images: @js($galleryUrls),
            title: @js($title ?? 'Chefchaouen Photo'),
            activeIndex: 0,
            lightboxOpen: false,
            select(idx) {
                this.activeIndex = idx;
            },
            openLightbox(idx = null) {
                if (idx !== null) this.activeIndex = idx;
                this.lightboxOpen = true;
                document.body.classList.add('overflow-hidden');
            },
            closeLightbox() {
                this.lightboxOpen = false;
                document.body.classList.remove('overflow-hidden');
            },
            prev() {
                this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
            },
            next() {
                this.activeIndex = (this.activeIndex + 1) % this.images.length;
            }
         }"
         @keydown.escape.window="if (lightboxOpen) closeLightbox()"
         @keydown.left.window="if (lightboxOpen) prev()"
         @keydown.right.window="if (lightboxOpen) next()">

        <!-- Main Featured Viewer -->
        <div class="relative aspect-[16/9] rounded-3xl overflow-hidden bg-slate-100 border border-slate-200 shadow-sm group">
            <img :src="images[activeIndex]"
                 :alt="title + ' (' + (activeIndex + 1) + ' of ' + images.length + ')'"
                 @click="openLightbox(activeIndex)"
                 class="w-full h-full object-cover cursor-zoom-in transition duration-500 group-hover:scale-[1.02]">

            <!-- Top-right Zoom / Count Badge -->
            <button type="button"
                    @click="openLightbox(activeIndex)"
                    aria-label="Open photo gallery lightbox"
                    class="absolute top-3.5 right-3.5 px-3 py-1.5 rounded-full bg-slate-950/65 hover:bg-slate-950/85 text-white text-xs font-semibold backdrop-blur-sm transition flex items-center gap-1.5 shadow">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7"/>
                </svg>
                <span x-text="images.length > 1 ? (activeIndex + 1) + ' / ' + images.length + ' · Zoom' : 'Zoom'"></span>
            </button>

            <template x-if="images.length > 1">
                <div>
                    <button type="button" @click.stop="prev()" aria-label="Previous photo"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/85 hover:bg-white text-slate-900 shadow-lg flex items-center justify-center transition opacity-90 sm:opacity-0 sm:group-hover:opacity-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button type="button" @click.stop="next()" aria-label="Next photo"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/85 hover:bg-white text-slate-900 shadow-lg flex items-center justify-center transition opacity-90 sm:opacity-0 sm:group-hover:opacity-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        <!-- Clickable Thumbnails Underneath Featured Photo -->
        <template x-if="images.length > 1">
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
                <template x-for="(imgUrl, idx) in images" :key="idx">
                    <div class="relative group aspect-[4/3] rounded-2xl overflow-hidden border transition cursor-pointer bg-slate-100"
                         :class="activeIndex === idx ? 'ring-2 ring-chaouen-600 border-chaouen-600 shadow-md' : 'border-slate-200 hover:border-chaouen-400 opacity-85 hover:opacity-100'">
                        <button type="button"
                                @click="select(idx)"
                                @dblclick="openLightbox(idx)"
                                :aria-label="'View photo ' + (idx + 1)"
                                class="w-full h-full block focus:outline-none">
                            <img :src="imgUrl"
                                 :alt="title + ' photo ' + (idx + 1)"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </button>
                        <button type="button"
                                @click.stop="openLightbox(idx)"
                                title="Enlarge photo"
                                aria-label="Enlarge photo"
                                class="absolute bottom-1.5 right-1.5 w-7 h-7 rounded-full bg-slate-950/65 hover:bg-chaouen-600 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm0-11v4m-2-2h4"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </template>

        <!-- Fullscreen Lightbox Modal -->
        <div x-show="lightboxOpen"
             x-cloak
             x-transition.opacity
             @click.self="closeLightbox()"
             class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative max-w-5xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl">
                <div class="relative bg-slate-950 flex items-center justify-center max-h-[75vh]">
                    <img :src="images[activeIndex]"
                         :alt="title + ' gallery photo ' + (activeIndex + 1)"
                         class="max-h-[75vh] w-auto max-w-full object-contain">

                    <button type="button" @click="closeLightbox()" aria-label="Close gallery"
                            class="absolute top-3.5 right-3.5 w-10 h-10 rounded-full bg-slate-900/75 hover:bg-slate-900 text-white flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <template x-if="images.length > 1">
                        <div>
                            <button type="button" @click="prev()" aria-label="Previous gallery photo"
                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/90 hover:bg-white text-slate-900 shadow-lg flex items-center justify-center transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button type="button" @click="next()" aria-label="Next gallery photo"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/90 hover:bg-white text-slate-900 shadow-lg flex items-center justify-center transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="p-4 sm:p-5 bg-white border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-base text-slate-900" x-text="title"></h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="'Photo ' + (activeIndex + 1) + ' of ' + images.length"></p>
                    </div>
                    <template x-if="images.length > 1">
                        <div class="flex items-center gap-2 overflow-x-auto max-w-full py-1">
                            <template x-for="(thumbUrl, tIdx) in images" :key="'lb-' + tIdx">
                                <button type="button" @click="select(tIdx)"
                                        :class="activeIndex === tIdx ? 'ring-2 ring-chaouen-600 opacity-100' : 'opacity-60 hover:opacity-100'"
                                        class="w-14 h-10 rounded-lg overflow-hidden border border-slate-200 shrink-0 transition">
                                    <img :src="thumbUrl" :alt="title + ' thumbnail ' + (tIdx + 1)" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
@endif
