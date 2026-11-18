<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicPage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'hero_badge',
        'content',
        'featured_image',
        'pdf_attachments',
        'meta_title',
        'meta_description',
        'show_in_header',
        'show_in_footer',
        'is_gdpr_page',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'pdf_attachments' => 'array',
        'show_in_header' => 'boolean',
        'show_in_footer' => 'boolean',
        'is_gdpr_page' => 'boolean',
        'is_published' => 'boolean',
    ];
}
