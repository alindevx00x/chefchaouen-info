<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TouristRoute extends Model
{
    protected $fillable = [
        'title',
        'title_ar',
        'slug',
        'route_type',
        'difficulty',
        'estimated_walking_time',
        'distance_km',
        'elevation_gain',
        'start_point',
        'end_point',
        'best_season',
        'short_description',
        'detailed_info',
        'featured_image',
        'album_photos',
        'map_iframe',
        'map_query',
        'latitude',
        'longitude',
        'waypoints',
        'trail_highlights',
        'what_to_bring',
        'ai_prompt_question',
        'is_featured',
        'status',
        'suggested_by_name',
        'suggested_by_email',
        'suggested_notes',
        'rating_avg',
        'reviews_count',
    ];

    protected $casts = [
        'album_photos' => 'array',
        'waypoints' => 'array',
        'trail_highlights' => 'array',
        'what_to_bring' => 'array',
        'is_featured' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'rating_avg' => 'float',
    ];

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
        if (!empty($this->map_iframe)) {
            return $this->map_iframe;
        }
        $query = urlencode(($this->map_query ?: ($this->start_point ? $this->start_point . ' to ' . $this->end_point : $this->title)) . ' Chefchaouen Morocco');
        return '<iframe width="100%" height="440" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=' . $query . '&t=m&z=14&ie=UTF8&iwloc=&output=embed" class="w-full rounded-2xl"></iframe>';
    }

    public function getGoogleMapsExternalUrlAttribute(): string
    {
        if ($this->start_point && $this->end_point) {
            return 'https://www.google.com/maps/dir/?api=1&origin=' . urlencode($this->start_point . ', Chefchaouen') . '&destination=' . urlencode($this->end_point . ', Chefchaouen');
        }
        $query = urlencode(($this->map_query ?: $this->title) . ' Chefchaouen Morocco');
        return 'https://www.google.com/maps/search/?api=1&query=' . $query;
    }

    public function getRouteTypeLabelAttribute(): string
    {
        return $this->route_type === 'outside_city' ? 'Outside City (Province)' : 'Inside City (Medina)';
    }

    public function recalculateRating(): void
    {
        $approved = $this->approvedReviews();
        $count = $approved->count();
        $avg = $count > 0 ? round($approved->avg('rating'), 2) : 4.90;
        $this->update([
            'reviews_count' => $count,
            'rating_avg' => $avg,
        ]);
    }
}
