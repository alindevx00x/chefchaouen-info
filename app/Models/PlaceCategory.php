<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlaceCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'subtitle',
        'description',
        'door_image',
        'accent_color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function places(): HasMany
    {
        return $this->hasMany(Place::class, 'place_category_id');
    }

    public function publishedPlaces(): HasMany
    {
        return $this->hasMany(Place::class, 'place_category_id')->where('status', 'published');
    }
}
