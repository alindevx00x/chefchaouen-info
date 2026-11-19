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
            ['group' => 'general', 'key' => 'site_icon', 'value' => '/images/branding/site-icon.png'],
            ['group' => 'general', 'key' => 'site_favicon', 'value' => '/images/branding/site-favicon.png'],
            ['group' => 'general', 'key' => 'hero_title', 'value' => 'Chefchaouen Deeper Than Blue'],
            ['group' => 'general', 'key' => 'hero_subtitle', 'value' => 'Behind every blue door lies a story. Beyond every alley awaits a new perspective. From the timeless charm of the medina to Akchour and the Rif Mountains, discover layers of beauty beyond the blue walls.'],
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
                'is_default' => false,
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
                'selected_model' => 'recursive/flint-v1.0:free',
                'available_models' => ['recursive/flint-v1.0:free', 'openai/gpt-4o-mini', 'anthropic/claude-3.5-sonnet', 'google/gemini-2.5-flash', 'meta-llama/llama-3.3-70b-instruct'],
                'is_active' => true,
                'is_default' => true,
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

        // 5. Real Chefchaouen Businesses (40 Booking.com Stays + 10 real listings per remaining category = 120 total)
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
                'subtitle' => 'Welcome to the Blue Pearl of Morocco. Discover. Experience. Remember.',
                'hero_badge' => 'Deeper Than Blue',
                'content' => '<h1>Chefchaouen: Deeper Than Blue</h1>'
                    . '<p>Known around the world for its iconic blue streets, <strong>Chefchaouen</strong> is far more than a beautiful color. It is a destination where history, culture, nature, and authenticity come together to create an experience unlike any other. Nestled at roughly <strong>660 meters elevation</strong> between the twin Rif Mountain peaks of <strong>Jebel Tissouka</strong> and <strong>Jebel El-Kelaa</strong>, this enchanting town invites travelers to slow down, wander freely, and discover the soul of northern Morocco.</p>'
                    . '<blockquote>"Behind every blue door lies a story. Beyond every alley awaits a new perspective. From the timeless charm of the medina to the breathtaking landscapes of Akchour and the surrounding mountains, Chefchaouen reveals layers of beauty that go far beyond its famous blue walls."</blockquote>'
                    . '<h2>More Than a Destination</h2>'
                    . '<p>Chefchaouen is a place where <strong>Andalusian heritage</strong> meets <strong>Moroccan Rif tradition</strong>, where crisp mountain air blends with the aroma of local cuisine, fresh wood-fired bread, and sweet mint tea, and where every moment becomes a lasting memory. Founded in <strong>1471 (876 AH)</strong> by Moulay Ali Ben Rachid above Oued El Fouara and Ras El Maa, its Mediterranean-Rif mountain setting offers warm summers, crisp winters with snowy peaks, and lush spring valleys.</p>'
                    . '<h3>Discover More Than Blue</h3>'
                    . '<p><strong>Chefchaouen.info</strong> is your premium curated guide to exploring the very best of the Blue Pearl. Carefully crafted for modern travelers, our platform helps you discover:</p>'
                    . '<ul>'
                    . '<li><strong>Hidden gems</strong> beyond the famous blue streets and photography corners</li>'
                    . '<li><strong>Luxury and boutique accommodations</strong> (1471-era inspired riads, dars, and Booking.com top stays)</li>'
                    . '<li><strong>Authentic cultural experiences</strong> and encounters with local weavers, potters, and historians</li>'
                    . '<li><strong>Traditional Moroccan &amp; Andalusian cuisine</strong>, from wood-fired bread ovens to clay-pot tagines</li>'
                    . '<li><strong>Nature escapes, waterfalls, and mountain trails</strong> across Akchour and Talassemtane National Park</li>'
                    . '<li><strong>Personalized itineraries</strong> for every travel style (1-day highlights, slow travel, family, luxury, or backpacker)</li>'
                    . '<li><strong>Practical local insights &amp; AI guidance</strong> grounded exclusively in verified Chefchaouen records</li>'
                    . '</ul>'
                    . '<h3>A Destination Unlike Any Other</h3>'
                    . '<p>As sunlight dances across the blue lime-washed walls and mountain breezes drift through the medina, time seems to slow down. Every corner reveals a new photograph, every path leads to a new discovery, and every experience leaves a lasting impression. Because Chefchaouen is not simply a blue city: it is a place of stories, emotions, traditions, and unforgettable moments. <em>Discover. Experience. Remember. Your journey begins here.</em></p>',
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
                'subtitle' => 'From the 1471 Rif citadel of Banu Rachid to an Andalusian-Moroccan city painted in sky.',
                'hero_badge' => 'Historical Guide • 1471 / 876 AH',
                'content' => '<h2>What Does "Chefchaouen" Mean?</h2>'
                    . '<p>Resting between the twin Rif peaks of <strong>Jebel Tissouka</strong> and <strong>Jebel El-Kelaa</strong> at 600 to 700 meters above sea level (60 km from Tetouan and 100 km from Ceuta), the name <strong>Chefchaouen</strong> derives from Moroccan Darija <em>"Chouf"</em> (Look) and Tamazight <em>"Chaouen"</em> (Horns / Mountain Peaks): <strong>"Look at the Peaks."</strong> Over centuries, historical chronicles recorded it as <em>Xexuao, Xauen, Chauen, Chechaouen</em>, and <em>Chefchaouen</em>.</p>'
                    . '<h2>1471 to 1561: Citadel of Resistance &amp; Independent Emirate</h2>'
                    . '<p>In <strong>1471 CE (876 AH)</strong>, the same year Portuguese forces seized Asilah and Tangier, <strong>El-Hassan Ben Othman (Sidi Boujemaa)</strong> and his cousin <strong>Moulay Ali Ben Rachid El-Alami</strong> founded Chefchaouen above the spring of <strong>Ras El Maa</strong> and Oued El Fouara as a fortified mountain citadel safeguarding northern Morocco. Under the <strong>Banu Rachid Emirate</strong> (1471 to 1561), Chefchaouen flourished for more than nine decades as a semi-independent principality with its own administration, army, crimson banner, and coinage. One of its most celebrated figures was <strong>Sayyida al-Hurra</strong>, daughter of Moulay Ali Ben Rachid, who governed Tetouan for over three decades (1510 to 1542) and defended northern shores through diplomacy and maritime strength.</p>'
                    . '<blockquote>"Chefchaouen condenses five centuries into a single day for the visitor: a tapestry of shadows and bright lime-washed courtyards carrying the echo of Granada inside the Rif Mountains."</blockquote>'
                    . '<h2>Three Andalusian Waves &amp; Historic Coexistence</h2>'
                    . '<p>Chefchaouen welcomed three major waves of Andalusian Muslim and Jewish families arriving from Granada, Ronda, Córdoba, Seville, and Málaga, shaping every neighborhood you explore today:</p>'
                    . '<ul>'
                    . '<li><strong>First Wave (1483 CE / 888 AH):</strong> Established the artisan quarters of <strong>El Kharrazin</strong> and <strong>Sebbanine</strong> alongside <strong>Souika</strong>, where ~80 pioneering Andalusian families settled near the Kasbah.</li>'
                    . '<li><strong>Second Wave (1492 to 1494 CE / 898 AH):</strong> Following the fall of Granada, newcomers built <strong>Rif El-Andalus</strong> with multi-story terracotta-tiled houses, inner courtyards, wells, and citrus and mulberry trees.</li>'
                    . '<li><strong>Third Wave (1502 CE / 907 AH):</strong> Expanded toward the mountain spring along <strong>El Onsar</strong>, preserving the Andalusian hydraulic irrigation channels (<em>sawayiq</em>) and fountains.</li>'
                    . '<li><strong>The Mellah &amp; Tarbiaa Craft Heritage:</strong> Andalusian and Moroccan Jewish silversmiths, tailors, and spice merchants lived in harmony alongside Muslim neighbors, practicing fine crafts inside <strong>Tarbiaa</strong> behind Souika.</li>'
                    . '</ul>'
                    . '<h2>Landmarks, Gates, Water Mills &amp; Living Traditions</h2>'
                    . '<h3>1. Architectural Monuments</h3>'
                    . '<ol>'
                    . '<li><strong>The 1471 Kasbah:</strong> Covering <strong>7,378 m&sup2;</strong> (72m &times; 52m) with 11 perimeter towers, the Ronda guard walkway, serene Andalusian gardens, and the Ethnographic Museum.</li>'
                    . '<li><strong>The Grand Mosque (Al-Masjid Al-Aadam):</strong> Spanning over <strong>1,500 m&sup2;</strong> above Place Outa el-Hammam, distinguished by its ornate <strong>octagonal minaret</strong> (crafted by master builder El-Aqel) and four historic portals.</li>'
                    . '<li><strong>Historic Medina Gates:</strong> Eleven documented gates framed the fortified walls, including <strong>Bab El-Ain</strong>, <strong>Bab El-Souk</strong>, <strong>Bab El-Onsar</strong>, <strong>Bab El-Hammar</strong>, <strong>Bab El-Moqaddam</strong>, and <strong>Bab El-Mahrouk</strong>.</li>'
                    . '<li><strong>Traditional Hammams, Ovens &amp; Granada-Style Mills:</strong> From <strong>Hammam El-Bled</strong> (the city\'s oldest bathhouse) to <strong>14 historic wood-fired communal ovens</strong> (three restored by ATED) and <strong>21 riverside grain mills</strong> modeled after Granada\'s Río Genil along Oued Ras El Maa.</li>'
                    . '</ol>'
                    . '<h3>2. Arts, Gastronomy &amp; Reading the Blue Architecture</h3>'
                    . '<ul>'
                    . '<li><strong>Horseshoe Arches &amp; Carved Cedar:</strong> Andalusian <em>Al-Qaws</em> arches, painted cedar ceilings, and wrought-iron window grilles frame quiet residential alleys.</li>'
                    . '<li><strong>Blue Lime-Wash &amp; Zellige:</strong> Seasonal layers of sky, cobalt, and indigo pigment cool plaster walls while hand-cut zellige mosaic adorns neighborhood fountains like Place El Haouta.</li>'
                    . '<li><strong>Riffian Crafts &amp; Culinary Heritage:</strong> Famous for hand-loomed wool <em>djellabas</em>, striped <em>mendil</em> textiles, Chefchaouen embroidery (<em>Tenchifa</em> and <em>Taajiza</em>), pomegranate-tanned leather, slow-simmered clay tagines, <em>bissara</em>, and fresh <em>jben</em> goat cheese.</li>'
                    . '</ul>'
                    . '<p>Poets and writers including <strong>Abu Hafs Omar El Harraq</strong>, <strong>Amine Rihani (1939)</strong>, and Nobel Laureate <strong>Vicente Aleixandre (1955)</strong> praised Chefchaouen as a white-and-blue sanctuary suspended between sky and mountain water. Download our printable Historical &amp; Cultural Medina Guide PDF below before setting out on the Historic Route.</p>',
                'featured_image' => '/images/gallery/kasbah.jpg',
                'pdf_attachments' => [
                    ['title' => 'Chefchaouen Historical & Cultural Medina Guide (PDF)', 'file_path' => '/uploads/pdfs/chefchaouen-historical-guide.pdf', 'file_size' => '18 KB', 'description' => 'Printable architectural and historical guide from 1471 to present day.'],
                ],
                'meta_title' => 'History & Culture | Historical Guide of Chefchaouen',
                'meta_description' => 'Explore the 1471 founding of Chefchaouen, Banu Rachid emirate, Andalusian waves, historic gates, architecture, and cultural heritage.',
                'show_in_header' => true,
                'show_in_footer' => true,
                'is_gdpr_page' => false,
                'sort_order' => 2,
            ],
            [
                'title' => 'Travel Plans & Itineraries',
                'slug' => 'visit-plans',
                'subtitle' => 'Curated 1-day, 2-day, 3-day, budget, family, luxury, and cultural itineraries in Chefchaouen.',
                'hero_badge' => 'Trip Planner • MAD Budgets',
                'content' => '<h2>Plan Your Ideal Stay in the Blue Pearl</h2>'
                    . '<p>Whether you have a quick 24-hour stopover or three unhurried days to soak in Rif Mountain serenity, these field-tested itineraries match every pace and budget.</p>'
                    . '<h3>1. One-Day Highlights Plan (Top Spots Without Rush)</h3>'
                    . '<ul>'
                    . '<li><strong>08:30 AM (Outa el-Hammam Square):</strong> Begin in the quiet morning calm with traditional Moroccan breakfast and fresh mint tea before crowds arrive.</li>'
                    . '<li><strong>09:30 AM (1471 Kasbah &amp; Ethnographic Museum):</strong> Explore the Andalusian garden courtyard and climb the rampart tower for elevated medina panoramas.</li>'
                    . '<li><strong>10:30 AM (Blue Alleys &amp; Derb El-Asri):</strong> Wander freely without staring at your phone; photograph sculpted blue stairways, cedar doors, and Place El Haouta.</li>'
                    . '<li><strong>01:00 PM (Local Tagine or Couscous Lunch):</strong> Choose a cozy family kitchen in Bab Ssour or Souika rather than crowded tourist traps.</li>'
                    . '<li><strong>03:00 PM (Ras El Maa &amp; Sebbanine):</strong> Cool down beside the rushing mountain waterfall, Portuguese Bridge, and riverside juice stalls.</li>'
                    . '<li><strong>05:30 PM (Bouzaafar Spanish Mosque Sunset):</strong> Walk up the gentle trail 45 minutes before sunset for the iconic golden-hour panorama across the blue city.</li>'
                    . '<li><strong>07:30 PM (Rooftop Dinner):</strong> Close the day under lanterns at a medina rooftop terrace.</li>'
                    . '</ul>'
                    . '<h3>2. Two-Day Balanced Itinerary (Medina + Akchour Nature)</h3>'
                    . '<h4>Day 1: Blue Medina, Kasbah &amp; Golden Hour</h4>'
                    . '<p>Follow the 1-Day Highlights rhythm across <strong>Outa el-Hammam</strong>, the <strong>Kasbah</strong>, <strong>Derb El-Asri</strong>, <strong>Ras El Maa</strong>, and <strong>Bouzaafar Sunset</strong>.</p>'
                    . '<h4>Day 2: Akchour Waterfalls &amp; God\'s Bridge</h4>'
                    . '<ul>'
                    . '<li><strong>09:00 AM Departure:</strong> Catch a shared grand taxi from town (~45 minutes, <strong>20 MAD</strong> per person each way) or book a private round-trip taxi (<strong>300 to 500 MAD</strong> with wait time).</li>'
                    . '<li><strong>10:00 AM to 03:00 PM Trail Hike:</strong> Trek beneath limestone cliffs to <strong>God\'s Bridge (Pont de Dieu)</strong> and the emerald pools of Akchour Waterfalls; enjoy a fresh riverside tagine lunch.</li>'
                    . '<li><strong>05:00 PM Return:</strong> Head back before sunset to catch shared taxis easily, followed by evening tea on a quiet terrace.</li>'
                    . '</ul>'
                    . '<h3>3. Three-Day Slow Travel, Couples &amp; Family Style</h3>'
                    . '<p>Spread your visit gently across three days: dedicate <strong>Day 1</strong> to slow orientation, artisan weaving in El Kharrazin, and a rooftop tea ceremony; <strong>Day 2</strong> to <strong>Ras El Maa</strong>, a traditional 2-hour <strong>Moroccan Hammam ritual</strong>, and craft shopping in Bab Ssour; and <strong>Day 3</strong> to a relaxed riverside picnic at <strong>Akchour</strong> or a guided village visit to <strong>Azilan and Akoumi</strong> inside Talassemtane National Park.</p>'
                    . '<h3>4. Backpacker &amp; Travel Cost Guide (MAD)</h3>'
                    . '<table>'
                    . '<thead><tr><th>Expense Category</th><th>Backpacker Budget</th><th>Mid-Range Comfort</th><th>Notes</th></tr></thead>'
                    . '<tbody>'
                    . '<tr><td><strong>Meals &amp; Tea</strong></td><td>60 to 100 MAD / day</td><td>120 to 250 MAD / day</td><td>Street food, bissara &amp; tagines (35 to 120 MAD per meal)</td></tr>'
                    . '<tr><td><strong>Stays (1 Night)</strong></td><td>60 to 150 MAD</td><td>250 to 600+ MAD</td><td>Medina hostels vs boutique riads &amp; Booking.com stays</td></tr>'
                    . '<tr><td><strong>Local Transport</strong></td><td>20 to 40 MAD</td><td>100 to 500 MAD</td><td>Medina is 100% walkable; Akchour shared taxi is 20 MAD each way</td></tr>'
                    . '<tr><td><strong>Estimated Total</strong></td><td><strong>120 to 200 MAD / day</strong></td><td><strong>450 to 900 MAD / day</strong></td><td>Arrive early from Tangier (2.5h), Tetouan (1h 15m), or Fez (4h)</td></tr>'
                    . '</tbody>'
                    . '</table>'
                    . '<blockquote><strong>Insider Secret:</strong> Wake between 7:00 and 9:00 AM for empty blue alleys, visit a traditional furnished Chaoueni heritage house (~10 MAD entry), or ask our <strong>Chefchaouen Guide AI</strong> to build a custom plan for your exact group and dates.</blockquote>',
                'featured_image' => '/images/gallery/akchour.jpg',
                'pdf_attachments' => [],
                'meta_title' => 'Travel Plans & Itineraries | Chefchaouen.info',
                'meta_description' => '1-day, 2-day, 3-day, budget backpacker, family, and luxury itineraries for visiting Chefchaouen and Akchour.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => false,
                'sort_order' => 3,
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'subtitle' => 'Guidelines for using the Chefchaouen.info directory and travel resources.',
                'hero_badge' => 'Legal & Usage',
                'content' => '<h2>Terms of Use</h2><p>Welcome to <strong>Chefchaouen.info</strong>. By accessing our directory, interactive Google Map tools, route guides, and Chefchaouen Guide AI assistant, you agree to use these resources responsibly for personal travel planning.</p><h3>Directory &amp; Route Information</h3><p>While we verify local listings and trail conditions carefully, mountain weather and local business hours may change seasonally. Always verify trail conditions locally before hiking outside the city in the Rif Mountains.</p><h3>Community Reviews &amp; Place Suggestions</h3><p>Submitted reviews and local place or route suggestions are moderated by our editorial team before publication to keep information respectful, accurate, and helpful for all visitors.</p>',
                'pdf_attachments' => [],
                'meta_title' => 'Terms and Conditions | Chefchaouen.info',
                'meta_description' => 'Terms and conditions for using Chefchaouen.info directory, routes, and AI guide.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'subtitle' => 'How we protect visitor privacy and GDPR rights on Chefchaouen.info.',
                'hero_badge' => 'GDPR Compliance',
                'content' => '<h2>Privacy &amp; Data Protection</h2><p>Chefchaouen.info respects your privacy. We do not require visitor accounts to browse places, routes, or maps. When you submit a review, contact inquiry, newsletter subscription, or place suggestion, your email is used solely for moderation, replying to your inquiry, or sending updates you explicitly requested.</p><p>For any data access or deletion request under GDPR, please email <a href="mailto:contact@chefchaouen.info">contact@chefchaouen.info</a>.</p>',
                'pdf_attachments' => [],
                'meta_title' => 'Privacy Policy | Chefchaouen.info',
                'meta_description' => 'GDPR privacy policy for Chefchaouen.info.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => true,
                'sort_order' => 5,
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
                'sort_order' => 6,
            ],
            [
                'title' => 'Return & Refund Policy',
                'slug' => 'return-refund-policy',
                'subtitle' => 'Information regarding featured directory listings and partner inquiries.',
                'hero_badge' => 'Policy',
                'content' => '<h2>Return &amp; Refund Policy</h2><p>Chefchaouen.info is a curated travel directory. Visitors browse all places, routes, maps, and guides completely free of charge. For verified local business partners or promotional inquiries arranged directly via <a href="mailto:contact@chefchaouen.info">contact@chefchaouen.info</a>, terms are specified in each partner agreement.</p>',
                'pdf_attachments' => [],
                'meta_title' => 'Return & Refund Policy | Chefchaouen.info',
                'meta_description' => 'Return and refund policy for Chefchaouen.info partner listings.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => false,
                'sort_order' => 7,
            ],
            [
                'title' => 'Disclaimer',
                'slug' => 'disclaimer',
                'subtitle' => 'Travel safety and outdoor trail advisory.',
                'hero_badge' => 'Advisory',
                'content' => '<h2>Travel &amp; Hiking Disclaimer</h2><p>Outdoor routes in Chefchaouen Province (including Akchour, Jebel El-Kelaa, Jebel Yemma Sna, and Jebel El-Abiod) involve natural mountain terrain. Visitors are responsible for assessing their own fitness, wearing appropriate footwear, carrying water, and checking local weather conditions before setting out.</p>',
                'pdf_attachments' => [],
                'meta_title' => 'Disclaimer | Chefchaouen.info',
                'meta_description' => 'Trail safety and directory disclaimer for Chefchaouen.info.',
                'show_in_header' => false,
                'show_in_footer' => true,
                'is_gdpr_page' => false,
                'sort_order' => 8,
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
                'content' => '<h2>From Mountain Fortress to Living Canvas</h2><p>Walk into the Chefchaouen medina at <strong>7:30 AM</strong> and you hear brooms brushing cobblestones, water running down stone channels from Ras El Maa, and the soft brush of fresh blue lime-wash against sun-warmed plaster.</p><p>Founded in <strong>1471</strong> and enriched by Andalusian families after <strong>1483, 1492, and 1502</strong>, Chefchaouen’s shades of sky, cobalt, and deep ultramarine keep houses cool in summer while honoring centuries of shared craft and serenity.</p><h3>Three Ways to Experience the Medina Mindfully</h3><ul><li><strong>Wake with the sunrise (7:00 to 9:00 AM):</strong> Early morning offers peaceful alleys like Derb El-Asri and gentle golden light.</li><li><strong>Follow the Historic Route:</strong> Pair visual beauty with the 1471 Kasbah, Grand Mosque, and 14 historic communal ovens.</li><li><strong>Support local looms:</strong> Visit family weavers and cooperatives in El-Kharrazin and Sebbanine.</li></ul>',
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
                'excerpt' => 'Everything you need to know before heading into Talassemtane National Park: transport, taxi fares in MAD, trail forks, and riverside dining.',
                'content' => '<h2>Gateway to Talassemtane National Park</h2><p>Just <strong>45 minutes</strong> from Chefchaouen’s Bab El-Ain (550m altitude) lies one of North Africa’s most dramatic limestone valleys: <strong>Akchour</strong>. Beyond emerald swimming pools and waterfalls stands <strong>God’s Bridge (Pont de Dieu)</strong>, a monumental natural stone arch spanning Oued El Farda less than a kilometer from the Akchour dam.</p><h3>Practical Transport &amp; Trail Checklist</h3><ul><li><strong>Shared Grand Taxi:</strong> Around <strong>20 MAD per person</strong> each way (return before sunset to catch a shared ride easily).</li><li><strong>Private Wait-and-Return Taxi:</strong> Between <strong>300 and 500 MAD</strong> for the full round trip depending on season.</li><li><strong>Overnight Option:</strong> Riverside camping tents (~150 MAD) or mountain guesthouses in Azilan and Akchour (from ~200 MAD).</li><li><strong>What to Pack:</strong> Non-slip walking shoes, 2 liters of water, and cash for freshly prepared riverside tagines.</li></ul>',
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
                'excerpt' => 'From 14 traditional wood-fired bread ovens to rooftop cafés, taste the northern Moroccan dishes shaped by Andalusian and Rif heritage.',
                'content' => '<h2>Andalusian-Rif Flavors on Every Table</h2><p>Chefchaouen’s mountain terroir shines brightest at breakfast and lunch: creamy local goat cheese (<em>Jben</em>), cold-pressed Rif olive oil, sun-dried figs and pomegranates, wild thyme honey, and slow-simmered clay tagines seasoned with saffron, ginger, and cinnamon.</p><h3>Must-Try Chaoueni Specialties</h3><ul><li><strong>Wood-Fired Bread &amp; Bissara:</strong> Steaming fava bean soup topped with local olive oil and cumin, paired with loaves baked in historic neighborhood ovens.</li><li><strong>Clay-Pot Tagines &amp; Almond Pastilla:</strong> Slow-cooked chicken with preserved lemon and olives, or sweet-savory Andalusian pastilla and honeyed <em>mhencha</em>.</li><li><strong>Riffian Mint Tea Ritual:</strong> Served high above the blue rooftops from Outa el-Hammam to Ras El Maa.</li></ul>',
                'featured_image' => '/images/gallery/moroccan-food.jpg',
                'reading_time_minutes' => 5,
                'is_featured' => false,
                'published_at' => now()->subDays(12),
            ],
            [
                'blog_category_id' => $blogCatHiking->id,
                'author_id' => $admin->id,
                'title' => 'Inside vs. Outside the City: Choosing the Right Chefchaouen Route',
                'slug' => 'choosing-the-right-chefchaouen-route',
                'excerpt' => 'Compare our easy medina heritage walks with Rif Mountain excursions like Azilan, Akoumi, El Qalaa, and Jebel El-Kelaa.',
                'content' => '<h2>Two Worlds in One Destination</h2><p>Short on time? Combine the <strong>Medina Blue Walk</strong> or <strong>Historic Route</strong> in the morning with the <strong>Bouzaafar Spanish Mosque Route</strong> before sunset. Have two or three days? Dedicate a full morning to <strong>Akchour Waterfalls</strong> or hike through cedar forests along the <strong>Chefchaouen to Azilan &amp; Akoumi</strong> corridor (passing Ain Tissamlal at 1,000m, Azilan at 1,270m, and the ancient fortified granaries of El Qalaa).</p><blockquote>Tip: For deep Rif Mountain routes across Talassemtane and Bouhachem parks, hiring a certified local mountain guide enriches both safety and cultural storytelling.</blockquote>',
                'featured_image' => '/images/gallery/spanish-mosque.jpg',
                'reading_time_minutes' => 5,
                'is_featured' => false,
                'published_at' => now()->subDays(15),
            ],
            [
                'blog_category_id' => $blogCatHistory->id,
                'author_id' => $admin->id,
                'title' => 'The Living History of Chefchaouen: From 1471 Citadel to Andalusian Pearl',
                'slug' => 'living-history-chefchaouen-1471-to-andalusia',
                'excerpt' => 'How Moulay Ali Ben Rachid, Sayyida al-Hurra, and three waves of Andalusian artisans forged a unique northern Moroccan sanctuary.',
                'content' => '<h2>Five Centuries Preserved in Stone and Sky</h2><p>Named <em>Chouf Chaouen</em> ("Look at the Peaks") for its cradle beneath Jebel Tissouka and Jebel El-Kelaa, Chefchaouen was built in <strong>1471</strong> as a resilient highland stronghold. For over ninety years under the <strong>Banu Rachid Emirate</strong>, it guarded the Rif passes between Tetouan and Fez.</p><h3>Quarters Built by Andalusian Waves</h3><ol><li><strong>Souika &amp; El Kharrazin:</strong> First residential and leathercraft neighborhoods beside the 1471 Kasbah.</li><li><strong>Rif El-Andalus (1492):</strong> Terracotta-roofed courtyard homes echoing Granada and Ronda.</li><li><strong>El Onsar &amp; Sebbanine:</strong> Linking the medina toward Ras El Maa\'s water mills and Portuguese Bridge.</li></ol><p>Explore the full architectural breakdown on our <a href="/page/history-culture">History &amp; Culture Guide</a>.</p>',
                'featured_image' => '/images/gallery/kasbah.jpg',
                'reading_time_minutes' => 6,
                'is_featured' => true,
                'published_at' => now()->subDays(18),
            ],
            [
                'blog_category_id' => $blogCatFood->id,
                'author_id' => $admin->id,
                'title' => 'How to Plan 1, 2, or 3 Days in Chefchaouen (Every Travel Budget)',
                'slug' => 'how-to-plan-1-2-3-days-chefchaouen-budget',
                'excerpt' => 'From a 120 to 200 MAD/day backpacker loop to slow 3-day family and luxury schedules, build your ideal itinerary.',
                'content' => '<h2>Smart Timing &amp; Daily Budgets</h2><p>Chefchaouen is easy to reach by CTM bus or grand taxi from <strong>Tangier (~2.5 hours)</strong>, <strong>Tetouan (~1 hr 15 min)</strong>, and <strong>Fez (~4 hours)</strong>. One overnight stay lets you see the highlights, while <strong>two nights</strong> gives you space for both the medina and Akchour Waterfalls.</p><h3>Quick Itinerary Pick</h3><ul><li><strong>1 Day:</strong> Outa el-Hammam breakfast, 1471 Kasbah, Derb El-Asri, Ras El Maa, Bouzaafar Sunset, rooftop dinner.</li><li><strong>2 Days:</strong> Day 1 Medina &amp; Heritage + Day 2 Akchour Waterfalls &amp; God\'s Bridge.</li><li><strong>Backpacker Daily Cost:</strong> 60 to 100 MAD meals, 60 to 100 MAD hostel stay, ~120 to 200 MAD total/day.</li></ul><p>Read every schedule in full on our <a href="/page/visit-plans">Travel Plans &amp; Itineraries</a> page.</p>',
                'featured_image' => '/images/gallery/plaza-uta.jpg',
                'reading_time_minutes' => 5,
                'is_featured' => false,
                'published_at' => now()->subDays(21),
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
