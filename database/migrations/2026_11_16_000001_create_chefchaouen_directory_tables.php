<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add role, phone, bio, avatar, is_active to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('avatar');
            $table->boolean('is_active')->default(true)->after('bio');
        });

        // 1. Settings Table (General, SEO, Code Injection, Email/SMTP/Brevo, Default AI Config)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general')->index();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // 2. AI Providers Table (Groq, OpenAI, Gemini, DeepSeek, OpenRouter, Custom)
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Groq, OpenAI, Google Gemini, DeepSeek, OpenRouter
            $table->string('slug')->unique(); // groq, openai, gemini, deepseek, openrouter
            $table->string('base_url')->nullable();
            $table->text('api_key')->nullable();
            $table->string('selected_model')->nullable();
            $table->json('available_models')->nullable(); // fetched or pre-populated list
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->string('last_test_status')->nullable(); // success, error
            $table->text('last_test_message')->nullable();
            $table->timestamp('last_tested_at')->nullable();
            $table->timestamps();
        });

        // 3. Place Categories Table
        Schema::create('place_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('door_image')->nullable();
            $table->string('accent_color')->default('#1d4ed8');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Places Table
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_category_id')->constrained('place_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('album_photos')->nullable();
            $table->text('direction_map_iframe')->nullable();
            $table->string('map_query')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('address')->nullable();
            $table->string('neighborhood')->default('Old Medina');
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->json('amenities')->nullable();
            $table->json('highlights')->nullable();
            $table->string('price_range')->default('$$'); // $, $$, $$$, Free
            $table->string('badge_text')->nullable(); // e.g., Hidden Gem, Rooftop View, Local Craft
            $table->boolean('is_verified_partner')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_hidden_gem')->default(false);
            $table->string('status')->default('published')->index(); // published, draft, pending
            $table->string('suggested_by_name')->nullable();
            $table->string('suggested_by_email')->nullable();
            $table->text('suggested_notes')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(4.80);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->timestamps();
        });

        // 5. Tourist Routes Table
        Schema::create('tourist_routes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('slug')->unique();
            $table->string('route_type')->default('inside_city')->index(); // inside_city, outside_city
            $table->string('difficulty')->default('Easy'); // Easy, Moderate, Challenging
            $table->string('estimated_walking_time')->nullable(); // e.g. 2 - 3 hours
            $table->string('distance_km')->nullable(); // e.g. 3.5 km
            $table->string('elevation_gain')->nullable(); // e.g. +240m
            $table->string('start_point')->nullable();
            $table->string('end_point')->nullable();
            $table->string('best_season')->default('Year-round (Spring & Autumn ideal)');
            $table->text('short_description')->nullable();
            $table->longText('detailed_info')->nullable(); // Managed with TinyMCE
            $table->string('featured_image')->nullable();
            $table->json('album_photos')->nullable();
            $table->text('map_iframe')->nullable();
            $table->string('map_query')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('waypoints')->nullable();
            $table->json('trail_highlights')->nullable();
            $table->json('what_to_bring')->nullable();
            $table->string('ai_prompt_question')->nullable();
            $table->boolean('is_featured')->default(true);
            $table->string('status')->default('published')->index(); // published, draft, pending
            $table->string('suggested_by_name')->nullable();
            $table->string('suggested_by_email')->nullable();
            $table->text('suggested_notes')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(4.90);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->timestamps();
        });

        // 6. Reviews Table (Polymorphic for Places and TouristRoutes)
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable'); // reviewable_type, reviewable_id
            $table->string('visitor_name');
            $table->string('visitor_email');
            $table->string('visitor_country')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('title')->nullable();
            $table->text('comment');
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->string('status')->default('pending')->index(); // pending, approved, rejected
            $table->timestamps();
        });

        // 7. Public Pages Table (TinyMCE + PDF attachments)
        Schema::create('public_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->string('hero_badge')->nullable();
            $table->longText('content')->nullable(); // TinyMCE HTML
            $table->string('featured_image')->nullable();
            $table->json('pdf_attachments')->nullable(); // [{title, file_path, file_size, description}]
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('show_in_header')->default(false);
            $table->boolean('show_in_footer')->default(true);
            $table->boolean('is_gdpr_page')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 8. Gallery Photos Table
        Schema::create('gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('Medina'); // Medina, Nature & Akchour, Architecture, Culture & Crafts, Gastronomy
            $table->string('image_path');
            $table->text('caption')->nullable();
            $table->string('photographer')->nullable();
            $table->string('location_tag')->nullable();
            $table->boolean('is_featured')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 9. Blog Categories Table
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 10. Blog Posts Table (TinyMCE)
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable(); // TinyMCE HTML
            $table->string('featured_image')->nullable();
            $table->integer('reading_time_minutes')->default(4);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 11. Newsletters Table
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('status')->default('subscribed'); // subscribed, unsubscribed
            $table->string('source')->default('homepage');
            $table->timestamps();
        });

        // 12. Contact Submissions Table
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->string('inquiry_type')->default('General Inquiry');
            $table->text('message');
            $table->string('status')->default('new')->index(); // new, read, replied
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        // 13. FAQs Table
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('General');
            $table->string('question');
            $table->text('answer');
            $table->boolean('show_on_home')->default(true);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 14. Chatbot Logs Table
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->text('user_message');
            $table->longText('bot_response');
            $table->string('provider_used')->nullable();
            $table->string('model_used')->nullable();
            $table->boolean('was_blocked_off_topic')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('contact_submissions');
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('gallery_photos');
        Schema::dropIfExists('public_pages');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('tourist_routes');
        Schema::dropIfExists('places');
        Schema::dropIfExists('place_categories');
        Schema::dropIfExists('ai_providers');
        Schema::dropIfExists('settings');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'avatar', 'bio', 'is_active']);
        });
    }
};
