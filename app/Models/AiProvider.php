<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'api_key',
        'selected_model',
        'available_models',
        'is_active',
        'is_default',
        'last_test_status',
        'last_test_message',
        'last_tested_at',
    ];

    protected $casts = [
        'available_models' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'last_tested_at' => 'datetime',
    ];

    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first() ?? static::where('is_active', true)->first();
    }
}
