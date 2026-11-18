<?php

namespace Database\Seeders;

use App\Models\AiProvider;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactSubmission;
use App\Models\Faq;
use App\Models\GalleryPhoto;
use App\Models\Newsletter;
use App\Models\Place;
use App\Models\PlaceCategory;
use App\Models\PublicPage;
use App\Models\Review;
use App\Models\Setting;
use App\Models\TouristRoute;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Primary Admin Account (hidden from public UI)
        $admin = User::updateOrCreate(
            ['email' => 'admin@chefchaouen.info'],
            [
                'name' => 'Chefchaouen Platform Admin',
                'password' => Hash::make('Chefchaouen@2026'),
                'role' => 'super_admin',
                'bio' => 'Lead curator & administrator of Chefchaouen.info | Deeper Than Blue.',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Site Settings (General, Email, Full SEO & Code Injections)
        $defaultSettings = [
            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Chefchaouen.info'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Deeper Than Blue | The Blue Pearl Travel Directory'],
            ['group' => 'general', 'key' => 'contact_email', 'value' => 'contact@chefchaouen.info'],
            ['group' => 'general', 'key' => 'site_icon', 'value' => ''],
            ['group' => 'general', 'key' => 'site_favicon', 'value' => ''],
            ['group' => 'general', 'key' => 'hero_title', 'value' => 'Chefchaouen Deeper Than Blue'],
            ['group' => 'general', 'key' => 'hero_subtitle', 'value' => 'Welcome to the Blue Pearl of Morocco. Discover hand-picked medina places, Rif Mountain routes, and local stories.'],
            ['group' => 'general', 'key' => 'hero_image', 'value' => '/images/gallery/hero-medina.jpg'],
            ['group' => 'general', 'key' => 'social_instagram', 'value' => 'https://instagram.com/chefchaouen.info'],
            ['group' => 'general', 'key' => 'social_facebook', 'value' => 'https://facebook.com/chefchaouen.info'],
            ['group' => 'general', 'key' => 'social_youtube', 'value' => 'https://youtube.com/@chefchaoueninfo'],
            ['group' => 'general', 'key' => 'social_tiktok', 'value' => 'https://tiktok.com/@chefchaouen.info'],
            ['group' => 'general', 'key' => 'gdpr_notice_text', 'value' => 'We use essential cookies and Google Translate preferences to enhance your journey across Chefchaouen.info.'],

            // Email (Custom SMTP & Brevo)
            ['group' => 'email', 'key' => 'mail_driver', 'value' => 'log'], // log, smtp, brevo_smtp, brevo_api
            ['group' => 'email', 'key' => 'mail_from_address', 'value' => 'contact@chefchaouen.info'],
            ['group' => 'email', 'key' => 'mail_from_name', 'value' => 'Chefchaouen.info'],
            ['group' => 'email', 'key' => 'smtp_host', 'value' => 'smtp.mailtrap.io'],
            ['group' => 'email', 'key' => 'smtp_port', 'value' => '587'],
            ['group' => 'email', 'key' => 'smtp_encryption', 'value' => 'tls'],
            ['group' => 'email', 'key' => 'smtp_username', 'value' => ''],
            ['group' => 'email', 'key' => 'smtp_password', 'value' => ''],
            ['group' => 'email', 'key' => 'brevo_smtp_login', 'value' => ''],
            ['group' => 'email', 'key' => 'brevo_api_key', 'value' => ''],

            // Full SEO & Code Injection
            ['group' => 'seo', 'key' => 'seo_meta_title', 'value' => 'Chefchaouen.info | Deeper Than Blue · Places, Routes & Interactive Map'],
            ['group' => 'seo', 'key' => 'seo_meta_description', 'value' => 'Your curated travel directory for Chefchaouen, Morocco. Explore verified restaurants, riads, hidden medina gems, Rif hiking routes, and our AI local guide.'],
            ['group' => 'seo', 'key' => 'seo_meta_keywords', 'value' => 'Chefchaouen, Blue Pearl Morocco, Akchour waterfalls, Chefchaouen routes, Chefchaouen riads, Rif Mountains, Morocco travel guide'],
            ['group' => 'seo', 'key' => 'seo_og_image', 'value' => '/images/gallery/hero-medina.jpg'],
            ['group' => 'seo', 'key' => 'seo_robots', 'value' => 'index, follow'],
            ['group' => 'seo', 'key' => 'seo_canonical_base', 'value' => 'https://chefchaouen.info'],
            ['group' => 'seo', 'key' => 'seo_google_verification', 'value' => ''],
            ['group' => 'seo', 'key' => 'code_head_injection', 'value' => ''],
            ['group' => 'seo', 'key' => 'code_body_injection', 'value' => ''],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 3. AI Providers (Groq, OpenAI, Gemini, DeepSeek, OpenRouter)
        $providers = [
            [
                'name' => 'Groq Cloud',
                'slug' => 'groq',
                'base_url' => 'https://api.groq.com/openai/v1',
                'selected_model' => 'llama-3.3-70b-versatile',
                'available_models' => ['llama-3.3-70b-versatile', 'llama-3.1-8b-instant', 'mixtral-8x7b-32768', 'gemma2-9b-it'],
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'OpenAI',
                'slug' => 'openai',
                'base_url' => 'https://api.openai.com/v1',
                'selected_model' => 'gpt-4o-mini',
                'available_models' => ['gpt-4o-mini', 'gpt-4o', 'gpt-4.1-mini', 'gpt-4.1'],
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Google Gemini',
                'slug' => 'gemini',
                'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'selected_model' => 'gemini-2.5-flash',
                'available_models' => ['gemini-2.5-flash', 'gemini-2.5-pro', 'gemini-2.0-flash'],
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'DeepSeek',
                'slug' => 'deepseek',
                'base_url' => 'https://api.deepseek.com/v1',
                'selected_model' => 'deepseek-chat',
                'available_models' => ['deepseek-chat', 'deepseek-reasoner'],
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'OpenRouter',
                'slug' => 'openrouter',
                'base_url' => 'https://openrouter.ai/api/v1',
                'selected_model' => 'openai/gpt-4o-mini',
                'available_models' => ['openai/gpt-4o-mini', 'anthropic/claude-3.5-sonnet', 'google/gemini-2.5-flash', 'meta-llama/llama-3.3-70b-instruct'],
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($providers as $p) {
            AiProvider::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 4. Place Categories (Displayed as Arched Doors on Homepage)
        $categoriesData = [
            ['name' => 'Restaurants', 'slug' => 'restaurants', 'icon' => 'utensils', 'subtitle' => 'Tagines & Rif Flavors', 'door_image' => '/images/gallery/moroccan-food.jpg', 'accent_color' => '#1d4ed8', 'sort_order' => 1],
            ['name' => 'Cafés', 'slug' => 'cafes', 'icon' => 'coffee', 'subtitle' => 'Mint Tea & Rooftops', 'door_image' => '/images/gallery/moroccan-cafe.jpg', 'accent_color' => '#0284c7', 'sort_order' => 2],
            ['name' => 'Hotels', 'slug' => 'hotels', 'icon' => 'bed', 'subtitle' => 'Riads & Boutique Stays', 'door_image' => '/images/gallery/moroccan-riad.jpg', 'accent_color' => '#1e40af', 'sort_order' => 3],
            ['name' => 'Shopping', 'slug' => 'shopping', 'icon' => 'shopping-bag', 'subtitle' => 'Weaving & Leather Crafts', 'door_image' => '/images/gallery/zellige.jpg', 'accent_color' => '#0369a1', 'sort_order' => 4],
            ['name' => 'Activities', 'slug' => 'activities', 'icon' => 'compass', 'subtitle' => 'Workshops & Tours', 'door_image' => '/images/gallery/blue-stairs.jpg', 'accent_color' => '#2563eb', 'sort_order' => 5],
            ['name' => 'Historical Places', 'slug' => 'historical-places', 'icon' => 'landmark', 'subtitle' => '15th-Century Heritage', 'door_image' => '/images/gallery/kasbah.jpg', 'accent_color' => '#1e3a8a', 'sort_order' => 6],
            ['name' => 'Hiking & Nature', 'slug' => 'hiking-nature', 'icon' => 'mountain', 'subtitle' => 'Rif Peaks & Waterfalls', 'door_image' => '/images/gallery/akchour.jpg', 'accent_color' => '#0f766e', 'sort_order' => 7],
            ['name' => 'Parking', 'slug' => 'parking', 'icon' => 'car', 'subtitle' => 'Medina Gates & Access', 'door_image' => '/images/gallery/parking-town.jpg', 'accent_color' => '#334155', 'sort_order' => 8],
            ['name' => 'Services', 'slug' => 'services', 'icon' => 'life-buoy', 'subtitle' => 'Guides, Transfers & Aid', 'door_image' => '/images/gallery/medina-door.jpg', 'accent_color' => '#0ea5e9', 'sort_order' => 9],
        ];

        $categoryMap = [];
        foreach ($categoriesData as $c) {
            $cat = PlaceCategory::updateOrCreate(['slug' => $c['slug']], $c);
            $categoryMap[$c['slug']] = $cat->id;
        }

        // 5. Real Chefchaouen Businesses (10 real listings per category = 90 total)
        $placesData = RealDirectoryData::getPlaces($categoryMap);
        $realPlaceSlugs = collect($placesData)->pluck('slug')->all();
        Place::whereNotIn('slug', $realPlaceSlugs)->delete();

        foreach ($placesData as $place) {
            Place::updateOrCreate(['slug' => $place['slug']], $place);
        }

        // 6. Real Tourist Routes (10 Inside & Outside City routes from GetYourGuide & Rif trails)
        $routesData = RealDirectoryData::getRoutes();
        $realRouteSlugs = collect($routesData)->pluck('slug')->all();
        TouristRoute::whereNotIn('slug', $realRouteSlugs)->delete();

        foreach ($routesData as $route) {
            TouristRoute::updateOrCreate(['slug' => $route['slug']], $route);
        }

        // 7. Public Pages (TinyMCE managed + PDF attachments)
        $pagesData = [
            [
                'title' => 'About Chefchaouen.info',
                'slug' => 'about',
                'subtitle' => 'Your curated local companion to the Blue Pearl of Morocco.',
                'hero_badge' => 'Made With Care',
                'content' => '<h2>Chefchaouen: Deeper Than Blue</h2><p>Known around the world for its iconic blue streets, Chefchaouen is far more than a beautiful color. It is a destination where history, culture, nature, and authenticity come together to create an experience unlike any other. Nestled in the heart of the Rif Mountains, this enchanting town invites travelers to slow down, wander freely, and discover the soul of Morocco.</p><p><strong>Chefchaouen.info</strong> is your premium guide to exploring the very best of the Blue Pearl, connecting curious travelers with verified local riads, authentic restaurants, Rif mountain trails, and hidden corners of the medina.</p><h3>What We Curate</h3><ul><li>Hidden gems beyond the famous blue streets</li><li>Boutique riads and traditional Moroccan dining</li><li>Inside-medina walks and Talassemtane mountain routes</li><li>An AI Travel Assistant grounded exclusively in our local directory</li></ul>',
                'featured_image' => '/images/gallery/hero-medina.jpg',
                'pdf_attachments' => [
                    ['title' => 'Chefchaouen Visitor Pocket Overview (PDF)', 'file_path' => '/uploads/pdfs/chefchaouen-historical-guide.pdf', 'file_size' => '18 KB', 'description' => 'Quick orientation guide for first-time visitors to Chefchaouen.'],
                ],
                'meta_title' => 'About Us | Chefchaouen.info',
                'meta_description' => 'Learn about Chefchaouen.info, our mission to highlight authentic Rif heritage, local businesses, and curated routes.',
                'show_in_header' => true,
                'show_in_footer' => true,
                'is_gdpr_page' => false,
                'sort_order' => 1,
            ],
            [
                'title' => 'History & Culture',
                'slug' => 'history-culture',
                'subtitle' => 'From a 15th-century fortress to a city painted in sky.',
                'hero_badge' => 'Historical Guide • 1471',
                'content' => '<h2>From a 15th-Century Fortress to a City Painted in Sky</h2><p>Founded in <strong>1471</strong> by Moulay Ali Ben Rachid as a mountain fortress in the western Rif, Chefchaouen grew rapidly when Andalusian Muslim and Jewish families arrived after <strong>1492</strong>. Their craftsmanship (horseshoe arches, hand-cut zellige tilework, carved cedar lintels, and lime-washed walls) shapes the quiet lanes you walk today.</p><h3>Reading the Architecture</h3><ul><li><strong>Horseshoe Arch (Al-Qaws):</strong> Framing courtyards and mosque portals in unmistakable Andalusian geometry.</li><li><strong>Zellige Tilework:</strong> Geometric mosaic work adorning fountains like Place El Haouta and historic riads.</li><li><strong>Blue Lime-Wash:</strong> Layers of sky, indigo, and cobalt pigment applied seasonally to cool walls and evoke tranquility.</li><li><strong>Studded Cedar Doors:</strong> Hand-forged iron bosses and knockers crafted to signal heritage and hospitality.</li></ul><p>Download our printable Historical & Cultural Medina Guide PDF below before setting out on the Historic Route.</p>',
                'featured_image' => '/images/gallery/kasbah.jpg',
                'pdf_attachments' => [
                    ['title' => 'Chefchaouen Historical & Cultural Medina Guide (PDF)', 'file_path' => '/uploads/pdfs/chefchaouen-historical-guide.pdf', 'file_size' => '18 KB', 'description' => 'Printable architectural and historical guide from 1471 to present day.'],
                ],
                'meta_title' => 'History & Culture | Historical Guide of Chefchaouen',
                'meta_description' => 'Explore the 1471 founding of Chefchaouen, Andalusian architecture, zellige tilework, and download our historical PDF guide.',
                'show_in_header' => true,
                'show_in_footer' => true,
                'is_gdpr_page' => false,
                'sort_order' => 2,
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'subtitle' => 'Guidelines for using the Chefchaouen.info directory and travel resources.',
                'hero_badge' => 'Legal & Usage',
                'content' => '<h2>Terms of Use</h2><p>Welcome to <strong>Chefchaouen.info</strong>. By accessing our directory, interactive Google Map tools, route guides, and Chefchaouen Guide AI assistant, you agree to use these resources responsibly for personal travel planning.</p><h3>Directory & Route Information</h3><p>While we verify local listings and trail conditions carefully, mountain weather and local business hours may change seasonally. Always verify trail conditions locally before hiking outside the city in the Rif Mountains.</p><h3>Community Reviews & Place Suggestions</h3><p>Submitted reviews and local place or route suggestions are moderated by our editorial team before publication to keep information respectful, accurate, and helpful for all visitors.</p>',
                'pdf_attachments' => [],
                'meta_title' => 'Terms and Conditions | Chefchaouen.info',
                'meta_description' => 'Terms and conditions for using Chefchaouen.info directory, routes, and AI guide.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'subtitle' => 'How we protect visitor privacy and GDPR rights on Chefchaouen.info.',
                'hero_badge' => 'GDPR Compliance',
                'content' => '<h2>Privacy & Data Protection</h2><p>Chefchaouen.info respects your privacy. We do not require visitor accounts to browse places, routes, or maps. When you submit a review, contact inquiry, newsletter subscription, or place suggestion, your email is used solely for moderation, replying to your inquiry, or sending updates you explicitly requested.</p><p>For any data access or deletion request under GDPR, please email <a href="mailto:contact@chefchaouen.info">contact@chefchaouen.info</a>.</p>',
                'pdf_attachments' => [],
                'meta_title' => 'Privacy Policy | Chefchaouen.info',
                'meta_description' => 'GDPR privacy policy for Chefchaouen.info.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Cookie Policy',
                'slug' => 'cookie-policy',
                'subtitle' => 'Essential cookies and translation preferences.',
                'hero_badge' => 'GDPR & Cookies',
                'content' => '<h2>Cookie Policy</h2><p>Chefchaouen.info uses minimal essential cookies for security (CSRF protection), session continuity, remembering your GDPR consent banner dismissal, and saving your Google Translate language selection (English, Spanish, French, or Arabic).</p>',
                'pdf_attachments' => [],
                'meta_title' => 'Cookie Policy | Chefchaouen.info',
                'meta_description' => 'Cookie policy and GDPR cookie preferences for Chefchaouen.info.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Return & Refund Policy',
                'slug' => 'return-refund-policy',
                'subtitle' => 'Information regarding featured directory listings and partner inquiries.',
                'hero_badge' => 'Policy',
                'content' => '<h2>Return & Refund Policy</h2><p>Chefchaouen.info is a curated travel directory. Visitors browse all places, routes, maps, and guides completely free of charge. For verified local business partners or promotional inquiries arranged directly via <a href="mailto:contact@chefchaouen.info">contact@chefchaouen.info</a>, terms are specified in each partner agreement.</p>',
                'pdf_attachments' => [],
                'meta_title' => 'Return & Refund Policy | Chefchaouen.info',
                'meta_description' => 'Return and refund policy for Chefchaouen.info partner listings.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => false,
                'sort_order' => 6,
            ],
            [
                'title' => 'Disclaimer',
                'slug' => 'disclaimer',
                'subtitle' => 'Travel safety and outdoor trail advisory.',
                'hero_badge' => 'Advisory',
                'content' => '<h2>Travel & Hiking Disclaimer</h2><p>Outdoor routes in Chefchaouen Province (including Akchour, Jebel El-Kelaa, Jebel Yemma Sna, and Jebel El-Abiod) involve natural mountain terrain. Visitors are responsible for assessing their own fitness, wearing appropriate footwear, carrying water, and checking local weather conditions before setting out.</p>',
                'pdf_attachments' => [],
                'meta_title' => 'Disclaimer | Chefchaouen.info',
                'meta_description' => 'Trail safety and directory disclaimer for Chefchaouen.info.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => false,
                'sort_order' => 7,
            ],
        ];

        foreach ($pagesData as $page) {
            PublicPage::updateOrCreate(['slug' => $page['slug']], $page);
        }

        // 8. Gallery Photos
        $galleryData = [
            ['title' => 'Cobalt Alleyway at Golden Hour', 'category' => 'Medina', 'image_path' => '/images/gallery/hero-medina.jpg', 'location_tag' => 'Derb El-Azraq', 'caption' => 'Morning light illuminating sky-blue lime-washed steps.', 'sort_order' => 1],
            ['title' => 'Cascades d’Akchour', 'category' => 'Nature & Akchour', 'image_path' => '/images/gallery/akchour.jpg', 'location_tag' => 'Talassemtane National Park', 'caption' => 'Emerald mountain pools beneath limestone cliffs.', 'sort_order' => 2],
            ['title' => '15th-Century Kasbah Fortress', 'category' => 'Architecture', 'image_path' => '/images/gallery/kasbah.jpg', 'location_tag' => 'Plaza Uta el-Hammam', 'caption' => 'Historic ochre tower framed by Rif peaks.', 'sort_order' => 3],
            ['title' => 'Spanish Mosque Sunset Panorama', 'category' => 'Medina', 'image_path' => '/images/gallery/spanish-mosque.jpg', 'location_tag' => 'Bouzaafer Hill', 'caption' => 'Iconic overlook across the entire Blue Pearl.', 'sort_order' => 4],
            ['title' => 'Studded Cedar Horseshoe Door', 'category' => 'Architecture', 'image_path' => '/images/gallery/medina-door.jpg', 'location_tag' => 'El Haouta Quarter', 'caption' => 'Andalusian craftsmanship in carved cedar and iron.', 'sort_order' => 5],
            ['title' => 'Ras El Maa Mountain Spring', 'category' => 'Nature & Akchour', 'image_path' => '/images/gallery/ras-el-maa.jpg', 'location_tag' => 'Ras El Maa', 'caption' => 'Where cold Rif spring water meets the eastern medina.', 'sort_order' => 6],
            ['title' => 'Plaza Uta el-Hammam Palms', 'category' => 'Culture & Crafts', 'image_path' => '/images/gallery/plaza-uta.jpg', 'location_tag' => 'Old Medina Heart', 'caption' => 'The lively gathering square beneath the Great Mosque.', 'sort_order' => 7],
            ['title' => 'Rif Mountain Valley Mist', 'category' => 'Nature & Akchour', 'image_path' => '/images/gallery/rif-mountains.jpg', 'location_tag' => 'Chefchaouen Province', 'caption' => 'Dramatic ridges surrounding the Blue City.', 'sort_order' => 8],
            ['title' => 'Traditional Clay-Pot Tagine', 'category' => 'Gastronomy', 'image_path' => '/images/gallery/moroccan-food.jpg', 'location_tag' => 'Bab Ssour', 'caption' => 'Slow-cooked Rif spices, preserved lemon, and olives.', 'sort_order' => 9],
            ['title' => 'Riffian Mint Tea Ritual', 'category' => 'Gastronomy', 'image_path' => '/images/gallery/moroccan-cafe.jpg', 'location_tag' => 'Medina Rooftop', 'caption' => 'Sweet green tea poured high over panoramic terraces.', 'sort_order' => 10],
            ['title' => 'Andalusian Riad Courtyard', 'category' => 'Architecture', 'image_path' => '/images/gallery/moroccan-riad.jpg', 'location_tag' => 'El Onsar', 'caption' => 'Tranquil courtyard tiling and carved plasterwork.', 'sort_order' => 11],
            ['title' => 'Hand-Cut Zellige Geometry', 'category' => 'Culture & Crafts', 'image_path' => '/images/gallery/zellige.jpg', 'location_tag' => 'Medina Fountain', 'caption' => 'Centuries-old ceramic mosaic patterns.', 'sort_order' => 12],
        ];

        foreach ($galleryData as $photo) {
            GalleryPhoto::updateOrCreate(['title' => $photo['title']], $photo);
        }

        // 9. Blog Categories & Posts
        $blogCatHistory = BlogCategory::updateOrCreate(['slug' => 'culture-heritage'], ['name' => 'Culture & Heritage', 'description' => 'Stories of Chefchaouen’s Andalusian history and blue architecture.']);
        $blogCatHiking = BlogCategory::updateOrCreate(['slug' => 'hiking-nature'], ['name' => 'Hiking & Nature', 'description' => 'Trail reports across Akchour, Jebel El-Kelaa, and the Rif Mountains.']);
        $blogCatFood = BlogCategory::updateOrCreate(['slug' => 'food-lifestyle'], ['name' => 'Food & Slow Travel', 'description' => 'Local Rif cuisine, riads, and hidden medina cafés.']);

        $posts = [
            [
                'blog_category_id' => $blogCatHistory->id,
                'author_id' => $admin->id,
                'title' => 'Why Every Blue Wall in Chefchaouen Has a Story',
                'slug' => 'why-every-blue-wall-in-chefchaouen-has-a-story',
                'excerpt' => 'Beyond Instagram fame, discover how Andalusian settlers, natural mineral pigments, and Rif Mountain light shaped Morocco’s Blue Pearl.',
                'content' => '<p>Walk into the Chefchaouen medina at 7:30 AM and you hear brooms brushing cobblestones, water running down stone channels at Ras El Maa, and the soft brush of fresh blue lime-wash against sun-warmed plaster.</p><p>Founded in 1471 and enriched by Andalusian arrivals after 1492, Chefchaouen’s shades of sky, cobalt, and deep ultramarine keep houses cool in summer while honoring centuries of local memory.</p><h3>Three Ways to Experience the Medina Mindfully</h3><ul><li><strong>Wake with the sunrise:</strong> Early morning offers quiet alleys and gentle light.</li><li><strong>Follow the Historic Route:</strong> Pair visual beauty with the 1471 Kasbah and Great Mosque.</li><li><strong>Support local looms:</strong> Visit family workshops in El-Kharrazin.</li></ul>',
                'featured_image' => '/images/gallery/blue-stairs.jpg',
                'reading_time_minutes' => 5,
                'is_featured' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'blog_category_id' => $blogCatHiking->id,
                'author_id' => $admin->id,
                'title' => 'A Day at Akchour Waterfalls & God’s Bridge: Complete Trail Planner',
                'slug' => 'day-at-akchour-waterfalls-gods-bridge-planner',
                'excerpt' => 'Everything you need to know before heading into Talassemtane National Park: transport, trail forks, footwear, and riverside dining.',
                'content' => '<p>Just 45 minutes from Chefchaouen’s Bab El-Ain lies one of North Africa’s most dramatic limestone valleys: <strong>Akchour</strong>.</p><p>Depart before 08:30 AM to enjoy peaceful emerald pools before midday visitors arrive. Wear shoes with solid wet-rock grip, pack 2 liters of water, and save room for a freshly prepared riverside tagine on the return walk.</p>',
                'featured_image' => '/images/gallery/akchour.jpg',
                'reading_time_minutes' => 6,
                'is_featured' => true,
                'published_at' => now()->subDays(7),
            ],
            [
                'blog_category_id' => $blogCatFood->id,
                'author_id' => $admin->id,
                'title' => 'Slow Mornings in Chefchaouen: Goat Cheese, Olive Oil & Mint Tea',
                'slug' => 'slow-mornings-in-chefchaouen-food-guide',
                'excerpt' => 'From traditional wood-fired bread ovens to rooftop cafés, taste the northern Moroccan dishes unique to the Rif Mountains.',
                'content' => '<p>Chefchaouen’s mountain terroir shines brightest at breakfast and lunch: creamy local goat cheese (<em>Jben</em>), cold-pressed Rif olive oil, wild thyme honey, and slow-simmered clay tagines.</p>',
                'featured_image' => '/images/gallery/moroccan-food.jpg',
                'reading_time_minutes' => 4,
                'is_featured' => false,
                'published_at' => now()->subDays(12),
            ],
            [
                'blog_category_id' => $blogCatHiking->id,
                'author_id' => $admin->id,
                'title' => 'Inside vs. Outside the City: Choosing the Right Chefchaouen Route',
                'slug' => 'choosing-the-right-chefchaouen-route',
                'excerpt' => 'Compare our 3 easy medina walks with 4 Rif Mountain summit and canyon trails to plan the perfect 2 to 4 day itinerary.',
                'content' => '<p>Short on time? Combine the <strong>Medina Blue Walk</strong> in the morning with the <strong>Spanish Mosque Route</strong> before sunset. Have three days? Dedicate a full morning to <strong>Akchour Waterfalls</strong> or challenge yourself on <strong>Jebel El-Kelaa</strong>.</p>',
                'featured_image' => '/images/gallery/spanish-mosque.jpg',
                'reading_time_minutes' => 4,
                'is_featured' => false,
                'published_at' => now()->subDays(18),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }

        // 10. FAQs
        $faqs = [
            ['category' => 'Planning', 'question' => 'When is the best time of year to visit Chefchaouen?', 'answer' => 'Spring (March–May) and Autumn (September–November) offer ideal daytime temperatures (18°C–25°C) for both exploring the medina and hiking Rif Mountain trails like Akchour and Jebel El-Kelaa.', 'sort_order' => 1],
            ['category' => 'Routes & Hiking', 'question' => 'Do I need a guide for the inside-city routes or Spanish Mosque?', 'answer' => 'No guide is required for our Inside City routes (Medina Walk, Historic Route, Spanish Mosque); you can follow our waypoints and Google Maps links easily. For challenging Outside City peaks like Jebel El-Kelaa or Jebel El-Abiod, hiring a certified local mountain guide is recommended.', 'sort_order' => 2],
            ['category' => 'Transport & Parking', 'question' => 'Can I drive inside the Chefchaouen Medina?', 'answer' => 'The old medina is 100% pedestrian and car-free. Visitors arriving by car should park at attended gates such as Parking Bab El-Ain or Place Mohammed V, listed in our Parking category.', 'sort_order' => 3],
            ['category' => 'Directory', 'question' => 'How can local businesses or travelers suggest a new place or route?', 'answer' => 'Anyone can submit a local gem or trail via our "Suggest a Place / Route" page. Submissions appear as drafts in the platform dashboard where our editorial team verifies details before publishing.', 'sort_order' => 4],
            ['category' => 'AI Travel Assistant', 'question' => 'What can I ask the Chefchaouen Guide Chatbot?', 'answer' => 'Our Chefchaouen Guide AI answers questions exclusively about Chefchaouen, including places to stay and eat, hiking routes, walking times, history, culture, and interactive map directions.', 'sort_order' => 5],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        }

        // 11. Sample Approved Reviews
        $firstPlace = Place::where('slug', 'restaurant-bab-ssour')->first();
        $secondPlace = Place::where('slug', 'lina-ryad-and-spa')->first();
        $spanishRoute = TouristRoute::where('slug', 'spanish-mosque-sunset-route')->first();
        $akchourRoute = TouristRoute::where('slug', 'akchour-waterfalls-route')->first();

        if ($firstPlace) {
            Review::updateOrCreate(
                ['reviewable_type' => Place::class, 'reviewable_id' => $firstPlace->id, 'visitor_email' => 'elena.m@example.com'],
                [
                    'visitor_name' => 'Elena Morales',
                    'visitor_country' => 'Spain',
                    'rating' => 5,
                    'title' => 'Unforgettable lemon chicken tagine',
                    'comment' => 'Quiet alley away from the busiest square, attentive hosts, and the goat cheese starter was incredible.',
                    'admin_reply' => 'Thank you for sharing your experience, Elena! Bab Ssour remains one of our favorite quiet corners.',
                    'replied_at' => now()->subDays(2),
                    'status' => 'approved',
                ]
            );
            $firstPlace->recalculateRating();
        }

        if ($secondPlace) {
            Review::updateOrCreate(
                ['reviewable_type' => Place::class, 'reviewable_id' => $secondPlace->id, 'visitor_email' => 'marc.dubois@example.com'],
                [
                    'visitor_name' => 'Marc Dubois',
                    'visitor_country' => 'France',
                    'rating' => 5,
                    'title' => 'Serene rooftop breakfast over the blue rooftops',
                    'comment' => 'The zellige courtyard is spotless and the hammam felt wonderful after hiking Akchour.',
                    'status' => 'approved',
                ]
            );
            $secondPlace->recalculateRating();
        }

        if ($spanishRoute) {
            Review::updateOrCreate(
                ['reviewable_type' => TouristRoute::class, 'reviewable_id' => $spanishRoute->id, 'visitor_email' => 'sarah.k@example.com'],
                [
                    'visitor_name' => 'Sarah Kensington',
                    'visitor_country' => 'United Kingdom',
                    'rating' => 5,
                    'title' => 'Magical golden hour over Chefchaouen',
                    'comment' => 'An easy 25-minute climb from Ras El Maa with the clearest view of the entire blue medina and Rif mountains.',
                    'admin_reply' => 'We recommend starting 45 minutes before sunset for the best light!',
                    'replied_at' => now()->subDays(1),
                    'status' => 'approved',
                ]
            );
            $spanishRoute->recalculateRating();
        }

        if ($akchourRoute) {
            Review::updateOrCreate(
                ['reviewable_type' => TouristRoute::class, 'reviewable_id' => $akchourRoute->id, 'visitor_email' => 'youssef.a@example.com'],
                [
                    'visitor_name' => 'Youssef Alami',
                    'visitor_country' => 'Morocco',
                    'rating' => 5,
                    'title' => 'Crystal-clear turquoise pools',
                    'comment' => 'Went early at 8 AM and had God’s Bridge almost to ourselves. Wear good grip shoes!',
                    'status' => 'approved',
                ]
            );
            $akchourRoute->recalculateRating();
        }

        // 12. Sample Newsletter & Contact Submission for Admin Dashboard demo
        Newsletter::updateOrCreate(['email' => 'traveler@example.com'], ['name' => 'Sofia Laurent', 'status' => 'subscribed', 'source' => 'homepage']);
        ContactSubmission::updateOrCreate(
            ['email' => 'partner@riadexample.ma'],
            [
                'name' => 'Karim Benjelloun',
                'subject' => 'Verified Partner Listing Inquiry',
                'inquiry_type' => 'Partner Verification',
                'message' => 'Hello Chefchaouen.info team, we run a family boutique riad near El Onsar and would love to become a Verified Partner on the directory.',
                'status' => 'new',
            ]
        );
    }
}
