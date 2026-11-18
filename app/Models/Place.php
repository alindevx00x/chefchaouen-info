<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Place extends Model
{
    protected $fillable = [
        'place_category_id',
        'title',
        'slug',
        'subtitle',
        'short_description',
        'description',
        'featured_image',
        'album_photos',
        'direction_map_iframe',
        'map_query',
        'latitude',
        'longitude',
        'opening_hours',
        'address',
        'neighborhood',
        'phone',
        'whatsapp',
        'website',
        'email',
        'amenities',
        'highlights',
        'price_range',
        'badge_text',
        'is_verified_partner',
        'is_featured',
        'is_hidden_gem',
        'status',
        'suggested_by_name',
        'suggested_by_email',
        'suggested_notes',
        'rating_avg',
        'reviews_count',
    ];

    protected $casts = [
        'album_photos' => 'array',
        'amenities' => 'array',
        'highlights' => 'array',
        'is_verified_partner' => 'boolean',
        'is_featured' => 'boolean',
        'is_hidden_gem' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'rating_avg' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PlaceCategory::class, 'place_category_id');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function approvedReviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable')->where('status', 'approved')->latest();
    }

    public function getMapIframeHtmlAttribute(): string
    {
        if (!empty($this->direction_map_iframe)) {
            return $this->direction_map_iframe;
        }
        $query = urlencode(($this->map_query ?: $this->title) . ' Chefchaouen Morocco');
        return '<iframe width="100%" height="420" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=' . $query . '&t=m&z=15&ie=UTF8&iwloc=&output=embed" class="w-full rounded-2xl"></iframe>';
    }

    public function getGoogleMapsExternalUrlAttribute(): string
    {
        $query = urlencode(($this->map_query ?: $this->title) . ' Chefchaouen Morocco');
        return 'https://www.google.com/maps/search/?api=1&query=' . $query;
    }

    public function recalculateRating(): void
    {
        $approved = $this->approvedReviews();
        $count = $approved->count();
        $avg = $count > 0 ? round($approved->avg('rating'), 2) : 4.80;
        $this->update([
            'reviews_count' => $count,
            'rating_avg' => $avg,
        ]);
    }
}
