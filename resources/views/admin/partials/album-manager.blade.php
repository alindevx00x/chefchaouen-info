@php
    $initialFeatured = old('featured_image_url', $featured ?? '');
    if ($errors->any() && old('album_photos_urls') !== null) {
        $initialAlbum = array_values(array_filter(array_map('trim', explode("\n", (string) old('album_photos_urls')))));
    } else {
        $initialAlbum = is_array($album ?? null) ? array_values(array_filter($album)) : [];
    }
    $fallbackImg = $fallback ?? '/images/gallery/medina-door.jpg';
@endphp

<div class="pt-5 border-t border-slate-100 space-y-5"
     x-data="{
        featuredUrl: @js($initialFeatured),
        fallbackUrl: @js($fallbackImg),
        photos: @js($initialAlbum),
        newPhotoUrl: '',
        dragIndex: null,
        showRaw: false,
        move(from, to) {
            if (to < 0 || to >= this.photos.length || from === to) return;
            const item = this.photos.splice(from, 1)[0];
            this.photos.splice(to, 0, item);
        },
        makeFeatured(idx) {
            const chosen = this.photos[idx];
            if (!chosen) return;
            const oldFeatured = (this.featuredUrl || '').trim();
            this.featuredUrl = chosen;
            this.photos.splice(idx, 1);
            if (oldFeatured && oldFeatured !== chosen && !this.photos.includes(oldFeatured)) {
                this.photos.push(oldFeatured);
            }
        },
        removePhoto(idx) {
            this.photos.splice(idx, 1);
        },
        addUrl() {
            const u = this.newPhotoUrl.trim();
            if (!u) return;
            if (!this.photos.includes(u)) {
                this.photos.push(u);
            }
            this.newPhotoUrl = '';
        },
        handleDrop(targetIdx) {
            if (this.dragIndex !== null && this.dragIndex !== targetIdx) {
                this.move(this.dragIndex, targetIdx);
            }
            this.dragIndex = null;
        },
        updateFromRaw(text) {
            this.photos = text.split('\n').map(s => s.trim()).filter(Boolean);
        }
     }">

    <input type="hidden" name="album_photos_submitted" value="1">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Featured Image Panel (4 cols) -->
        <div class="lg:col-span-4 bg-slate-50 rounded-2xl border border-slate-200 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-800 uppercase tracking-wider">Featured Image</label>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-chaouen-100 text-chaouen-800">Primary Cover</span>
            </div>

            <div class="aspect-[16/10] rounded-xl overflow-hidden border border-slate-200 bg-white">
                <img :src="featuredUrl || fallbackUrl" alt="Featured preview" class="w-full h-full object-cover">
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-semibold text-slate-600">Upload Replacement Image</label>
                <input type="file" name="featured_image_file" accept="image/*" class="w-full text-xs text-slate-600">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Or Featured Image Path / URL</label>
                <input type="text" name="featured_image_url" x-model="featuredUrl" placeholder="{{ $fallbackImg }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
            </div>
        </div>

        <!-- Multi-Image Album Reorder & Gallery Panel (8 cols) -->
        <div class="lg:col-span-8 bg-slate-50 rounded-2xl border border-slate-200 p-4 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Gallery Album Photos (<span x-text="photos.length"></span>)</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Drag cards or use arrows to reorder photos. Click ★ to promote an image to Featured Cover.</p>
                </div>
                <button type="button" @click="showRaw = !showRaw" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-[11px] font-semibold text-slate-700 hover:bg-slate-100">
                    <span x-text="showRaw ? 'Hide Raw URLs' : 'Edit Raw URLs'"></span>
                </button>
            </div>

            <!-- Sortable Photo Grid -->
            <template x-if="photos.length > 0">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <template x-for="(photoUrl, idx) in photos" :key="photoUrl + '-' + idx">
                        <div draggable="true"
                             @dragstart="dragIndex = idx"
                             @dragover.prevent
                             @drop.prevent="handleDrop(idx)"
                             @dragend="dragIndex = null"
                             :class="dragIndex === idx ? 'opacity-50 ring-2 ring-chaouen-500' : ''"
                             class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm flex flex-col cursor-move">
                            <div class="relative aspect-[4/3] bg-slate-100">
                                <img :src="photoUrl" alt="Gallery photo" class="w-full h-full object-cover">
                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded bg-slate-900/75 text-white text-[10px] font-bold" x-text="'#' + (idx + 1)"></span>
                            </div>
                            <div class="p-1.5 flex items-center justify-between gap-1 bg-white border-t border-slate-100">
                                <div class="flex items-center gap-0.5">
                                    <button type="button" @click="move(idx, idx - 1)" :disabled="idx === 0" title="Move Left"
                                            class="px-1.5 py-1 rounded bg-slate-100 hover:bg-slate-200 disabled:opacity-35 text-[11px] font-bold">←</button>
                                    <button type="button" @click="move(idx, idx + 1)" :disabled="idx === photos.length - 1" title="Move Right"
                                            class="px-1.5 py-1 rounded bg-slate-100 hover:bg-slate-200 disabled:opacity-35 text-[11px] font-bold">→</button>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="makeFeatured(idx)" title="Promote to Featured Image"
                                            class="px-1.5 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 text-[11px] font-bold">★</button>
                                    <button type="button" @click="removePhoto(idx)" title="Remove Photo"
                                            class="px-1.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-bold">✕</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="photos.length === 0">
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center text-xs text-slate-500">
                    No gallery album photos added yet. Upload files or add image URLs below.
                </div>
            </template>

            <!-- Upload New Files + Quick Add URL -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2 border-t border-slate-200/80">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Upload New Album Photos (Appended to End)</label>
                    <input type="file" name="album_photos_files[]" multiple accept="image/*" class="w-full text-xs text-slate-600">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Quick Add Existing Path / Image URL</label>
                    <div class="flex gap-1.5">
                        <input type="text" x-model="newPhotoUrl" @keydown.enter.prevent="addUrl()" placeholder="/images/gallery/hero-medina.jpg"
                               class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-xs">
                        <button type="button" @click="addUrl()" class="px-3 py-1.5 rounded-xl bg-chaouen-600 text-white text-xs font-bold">+ Add</button>
                    </div>
                </div>
            </div>

            <!-- Synchronized Raw Field Submitted With Form -->
            <div x-show="showRaw" x-cloak>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Album URLs (One URL per line)</label>
                <textarea rows="3"
                          :value="photos.join('\n')"
                          @input="updateFromRaw($event.target.value)"
                          class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-mono"></textarea>
            </div>
            <input type="hidden" name="album_photos_urls" :value="photos.join('\n')">
        </div>
    </div>
</div>
