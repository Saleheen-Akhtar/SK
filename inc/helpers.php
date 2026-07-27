<?php
/**
 * Sacred Kompass — Helper functions & default configurations
 */
defined('ABSPATH') || exit;

/* ── HELPERS — pure reads, zero writes, zero queries ── */
function sk_acf(string $key, mixed $fallback=''): mixed {
    if (function_exists('get_field')) {
        $val = get_field($key, 'option');
        if (is_string($val)) $val = trim($val);
        if ($val !== null && $val !== '' && $val !== [] && $val !== false) {
            return $val;
        }
    }
    $val = get_option('options_'.$key, null);
    if (is_string($val)) $val = trim($val);
    if ($val !== null && $val !== '' && $val !== [] && $val !== false) {
        return $val;
    }
    if ($fallback !== '') {
        return $fallback;
    }
    $defaults = sk_acf_defaults();
    if (isset($defaults[$key])) {
        return $defaults[$key];
    }
    return '';
}

function sk_option(string $key, mixed $fallback=''): mixed {
    return sk_acf('sk_'.$key, $fallback);
}

/**
 * Check whether a homepage section is currently enabled in the Section Manager.
 * Defaults to true (visible) when the option has never been saved.
 */
function sk_section_enabled(string $key): bool {
    $val = get_option('sk_show_' . $key, null);
    if ($val === null || $val === false) return true;
    return (bool) $val;
}

/**
 * Check whether a section is flagged as "Admin Only".
 * When true, the section is rendered only for logged-in users who can edit_posts.
 * Public visitors never see it.
 */
function sk_section_admin_only(string $key): bool {
    return (bool) get_option('sk_admin_only_' . $key, false);
}

/**
 * Map nav item URLs/labels to their homepage section keys.
 * Used to filter nav items when a section is disabled in the Section Manager.
 * Keys must match the $core array in sk_section_manager_page().
 */
function sk_nav_section_map(): array {
    return [
        '#about'           => 'about',
        '/#about'          => 'about',
        '/about/'          => 'about',
        '/about'           => 'about',
        '#art'             => 'art',
        '/#art'            => 'art',
        '#faq'             => 'faq',
        '/#faq'            => 'faq',
        '#journal-preview' => 'journal',
        '/#journal-preview'=> 'journal',
        '/journal/'        => 'journal',
        '/journal'         => 'journal',
        '#founders'        => 'founders',
        '/#founders'       => 'founders',
        '/collective/'     => 'founders',
        '/collective'      => 'founders',
        '#philosophy'      => 'philosophy',
        '/#philosophy'     => 'philosophy',
        '#contact'         => 'cta',
        '/#contact'        => 'cta',
        '#stories-preview' => 'stories_preview',
        '/#stories-preview'=> 'stories_preview',
        '#stories'         => 'stories_preview',
        '/#stories'        => 'stories_preview',
        '/stories/'        => 'stories_preview',
        '/stories'         => 'stories_preview',
        '#kerala-pujas'     => 'kerala_pujas',
        '/#kerala-pujas'    => 'kerala_pujas',
        '/kerala-pujas/'    => 'kerala_pujas',
        '/kerala-pujas'     => 'kerala_pujas',
    ];
}

function sk_repeater(string $key): array {
    if (function_exists('get_field')) {
        $acf_key = str_replace(['options_', '_json'], '', $key);
        $val = get_field($acf_key, 'option');
        if (is_array($val) && !empty($val)) {
            return $val;
        }
    }
    $json = get_option($key,'');
    if (!$json) return [];
    $data = json_decode($json, true);
    return (is_array($data) && !empty($data)) ? $data : [];
}

function sk_reading_time(int $post_id): string {
    $post = get_post($post_id);
    $words = str_word_count(wp_strip_all_tags($post ? $post->post_content : ''));
    return max(1,(int)ceil($words/200)).' min read';
}

function sk_logo_html(string $class='sk-logo-img'): string {
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) { 
        $img = wp_get_attachment_image($logo_id,'full',false,['class'=>$class,'alt'=>get_bloginfo('name')]); 
        if ($img) return $img; 
    }
    return '';
}

function sk_default_hero_pairs(): array {
    return [
        ['from' => 'Despair',          'to' => 'Hope'],
        ['from' => 'Business Failure', 'to' => 'Profitability'],
        ['from' => 'Resentment',       'to' => 'Forgiveness'],
        ['from' => 'Adversity',        'to' => 'Opportunity'],
        ['from' => 'Hatred',           'to' => 'Peace'],
        ['from' => 'Lonely',           'to' => 'Couplehood'],
        ['from' => 'Impulsive',        'to' => 'Aligned'],
        ['from' => 'Confusion',        'to' => 'Clarity'],
        ['from' => 'Stagnant',         'to' => 'Evolving'],
        ['from' => 'Mistrust',         'to' => 'Faith'],
        ['from' => 'Lethargy',         'to' => 'Vitality'],
    ];
}

/* ── DEFAULT DATA ── */
function sk_acf_defaults(): array {
    return [
        'sk_contact_url' => '/#contact',
        'sk_hero_eyebrow' => 'Sacred Kompass · Transformation',
        'sk_hero_label_from' => 'from',
        'sk_hero_label_to' => 'to',
        'sk_hero_cta1_text' => '',
        'sk_hero_cta1_url' => '/#contact',
        'sk_hero_cta2_text' => '',
        'sk_hero_cta2_url' => '/#contact',
        'sk_hero_bg_image' => '',
        'sk_hero_bg_image_mobile' => '',
        'sk_hero_bg_video' => '',

        'sk_hero_right_image' => '',
        'sk_about_tagline' => 'Exploring Your Inner Journey',
        'sk_about_org_descriptor' => 'An Organisation for Consciousness and Transformation',
        'sk_about_heading' => '',
        'sk_about_brand_bg_image' => '',
        'sk_about_brand_domain' => 'sacredkompass.org',
        'sk_about_bg_image' => '',
        'sk_about_bg_image_mobile' => '',

        'sk_about_bridge_copy' => '',
        'sk_about_body' => '',
        'sk_about_welcome_strip' => 'Welcome to Sacred Kompass where your next chapter begins',
        'sk_founders_eyebrow' => 'Our People',
        'sk_founders_heading' => 'The Guides Behind',
        'sk_founders_heading_em' => 'Sacred Kompass',
        'sk_founders_sub' => 'Two souls, one vision. Uniting Eastern wisdom and Western heart in service of conscious living.',
        'sk_founders_body' => 'From Vedic philosophy and sacred feminine wisdom to conscious leadership and non-violent communication — every guide brings a practice, not just a credential.',
        'sk_founders_hover_hint' => 'Meet the Collective',
        'sk_founders_cta_label' => 'Explore the Collective',
        'sk_founders_bg_image' => '',
        'sk_founders_bg_image_mobile' => '',

        'sk_founders_team_image' => '',
        'sk_founders_team_title' => 'The Collective',
        'sk_founders_team_subtitle' => 'Sacred Kompass Collective',
        'sk_art_eyebrow' => 'Art Therapy',
        'sk_art_heading' => 'Art for',
        'sk_art_heading_em' => 'Peace',
        'sk_art_sub' => 'Artworks created as part of therapeutic healing, mindfulness and transformation. Explore our active collections below.',
        'sk_art_cta_url' => '/#contact',
        'sk_art_bg_image' => '',
        'sk_art_bg_image_mobile' => '',

        'sk_philosophy_heading' => 'How We Work',
        'sk_philosophy_heading_em' => 'With You',
        'sk_philosophy_intro' => 'Every pathway begins with a single question: what is ready to be seen? These are the lenses we bring.',
        'sk_philosophy_bg_image' => '',
        'sk_philosophy_bg_image_mobile' => '',

        'sk_philosophy_title_image' => '',
        'sk_pujas_eyebrow' => 'Next Step... The Solution',
        'sk_pujas_heading' => 'Kerala Tantric Pujas',
        'sk_pujas_heading_em' => 'Sri Vidya Tradition',
        'sk_pujas_gold_image' => '',
        'sk_pujas_scroll_image' => '',
        'sk_pujas_intro_text' => 'Kerala Tantric Pujas of the Sri Vidya tradition are sacred rituals rooted in temple, mantra and Devi worship practices passed down through traditional lineages. These rites work with mantra, yantra, flame, and divine invocation to restore balance, protection, clarity and spiritual alignment.',
        'sk_pujas_circumstances_heading' => 'Explore circumstances these pujas are performed for',
        'sk_pujas_callout_text' => 'In the Sri Vidya tradition, rituals are performed with deep reverence to the Divine Feminine and are believed to soften karmic influences, strengthen spiritual energy and bring harmony into one’s life journey. At Sacred Kompass, these practices are conducted with sincerity, traditional understanding and spiritual care.',
        'sk_pujas_cta_text' => 'Enquire about a puja',
        'sk_pujas_cta_url' => '/#contact',
        'sk_pujas_cta2_text' => 'Learn how it works',
        'sk_pujas_cta2_url' => '/#contact',
        'sk_faq_heading_1' => '',
        'sk_faq_heading_em' => '',
        'sk_faq_sub' => '',
        'sk_faq_cta_label' => '',
        'sk_faq_bg_image' => '',
        'sk_faq_bg_image_mobile' => '',

        'sk_stories_preview_eyebrow' => '',
        'sk_stories_preview_heading' => '',
        'sk_stories_preview_sub' => '',
        'sk_stories_preview_bg_image' => '',
        'sk_stories_preview_bg_image_mobile' => '',

        'sk_stories_hero_title' => 'Stories for the<br>soul',
        'sk_stories_hero_sub' => 'Real stories from beautiful souls who chose themselves, followed their inner compass, and created meaningful change.',
        'sk_stories_badge_labels' => 'Real Journeys, Heartfelt Transformations, Lasting Impact',
        'sk_stories_cta_heading' => 'Your story can inspire change.',
        'sk_stories_cta_sub' => "If our work together has made an impact, we'd be honored to share your journey (anonymously if you prefer).",
        'sk_stories_cta_btn_label' => 'Share Your Story',
        'sk_stories_no_results' => 'No stories in this category yet.',
        'sk_stories_read_more' => 'Read Her Story',
        'sk_journal_preview_heading' => 'From the Journal',
        'sk_journal_preview_eyebrow' => 'Journal',
        'sk_journal_preview_see_all' => 'See all posts',
        'sk_cta_eyebrow' => '',
        'sk_cta_heading' => '',
        'sk_cta_sub' => '',
        'sk_cta_default_heading_l1' => '',
        'sk_cta_default_heading_l2' => '',
        'sk_cta_default_heading_em' => '',
        'sk_cta_card_eyebrow' => '',
        'sk_cta_card_subheading_1' => '',
        'sk_cta_card_subheading_em' => '',
        'sk_cta_ff_name_label' => 'Your Name',
        'sk_cta_ff_email_label' => 'Email Address',
        'sk_cta_ff_msg_label' => 'Your Message',
        'sk_cta_ff_submit_label' => 'Send',
        'sk_cta_ff_note' => '',
        'sk_cta_bg_image' => '',
        'sk_cta_bg_image_mobile' => '',

        'sk_forminator_form_id' => '412',
        'sk_webhook_url' => '',
        'sk_webhook_token' => '',
        'sk_nav_cta_label' => '',
        'sk_nav_cta_url' => '/#contact',
        'sk_footer_email' => 'collective@sacredkompass.org',
        'sk_footer_phone' => '+65 84343915',
        'sk_footer_address' => '557 Bedok North St. 3, Singapore',
        'sk_footer_tagline' => 'Ancient wisdom for the modern soul.',
        'sk_footer_copyright' => 'Sacred Kompass Collective · Singapore',
        'sk_footer_location_bar' => 'Bedok North, Singapore &nbsp;&middot;&nbsp; Online Worldwide',
        'sk_footer_col_navigate' => 'Navigate',
        'sk_footer_col_art' => 'Art for Peace',
        'sk_footer_col_connect' => 'Connect',
        'sk_footer_col_legal' => 'Legal',
        'sk_social_instagram' => '',
        'sk_social_facebook' => '',
        'sk_social_whatsapp' => '',
        'sk_collective_hero_eyebrow' => 'Sacred Kompass',
        'sk_collective_hero_sub' => 'Guides, teachers, and practitioners united by one vision: to help individuals, leaders, and organisations reconnect with their inner compass.',
        'sk_collective_founders_eyebrow' => 'The Founders',
        'sk_collective_founder_badge' => 'Founder',
        'sk_collective_founder_cta' => 'Book a Session',
        'sk_collective_team_eyebrow' => 'The Team',
        'sk_collective_cta_eyebrow' => 'Ready to begin?',
        'sk_collective_cta_heading_1' => 'Work with',
        'sk_collective_cta_heading_em' => 'our Guides',
        'sk_collective_cta_body' => "Every guide in the Collective offers sessions tailored to your journey. Reach out and we'll match you with the right person.",
        'sk_collective_cta_button' => 'Book a Discovery Call',
        'sk_seo_home_title' => 'Sacred Kompass — Where the Sacred Meets the Everyday',
        'sk_seo_home_desc' => 'Sacred Kompass is a transformative wellness and consciousness-based consultancy weaving Vedic astrology, meditation, and emotional resilience into modern life.',
        'sk_seo_og_image' => '',
        'sk_logo_url' => '',
        'sk_newsletter_disclaimer' => 'Sacred Kompass respects your privacy. Unsubscribe anytime.',
        'sk_hero_pairs_json' => wp_json_encode(sk_default_hero_pairs())
    ];
}

function sk_default_pillars(): array {
    return [
        [
            'pillar_num' => '01',
            'pillar_title' => 'Ancient Wisdom',
            'pillar_desc' => 'Rooted in Vedic philosophy and centuries of sacred contemplative tradition.',
            'pillar_image' => '',
            'pillar_img_position' => '',
        ],
        [
            'pillar_num' => '02',
            'pillar_title' => 'Compassionate Practice',
            'pillar_desc' => 'Nonviolent Communication and emotional resilience woven into how we meet the world.',
            'pillar_image' => '',
            'pillar_img_position' => '',
        ],
        [
            'pillar_num' => '03',
            'pillar_title' => 'Inner Stillness',
            'pillar_desc' => 'Meditation, breathwork, and the art of presence.',
            'pillar_image' => '',
            'pillar_img_position' => '',
        ],
        [
            'pillar_num' => '04',
            'pillar_title' => 'Jyotish Astrology',
            'pillar_desc' => "The luminous science of light and time — a sacred map of your soul's journey.",
            'pillar_image' => '',
            'pillar_img_position' => '',
        ],
        [
            'pillar_num' => '05',
            'pillar_title' => 'Sacred Feminine',
            'pillar_desc' => 'Honouring the intelligence of the feminine — cyclical, intuitive, embodied.',
            'pillar_image' => '',
            'pillar_img_position' => '',
        ],
    ];
}

function sk_default_founders(): array {
    return [['founder_name'=>'Kalai','founder_surname'=>'Somoo','founder_origin'=>'Singapore','founder_role'=>'Founder and Lead Guide','founder_bio'=>"Kalai founded Sacred Kompass with a vision to reconnect people to their inner wisdom.",'founder_tags'=>"Women's Wellness\nVedic Philosophy\nJyotish Astrology",'founder_image'=>''],['founder_name'=>'Christophe','founder_surname'=>'Grigri','founder_origin'=>'France','founder_role'=>'International Coordination and Communication','founder_bio'=>"Christophe brings decades of international experience bridging cultures through compassionate dialogue.",'founder_tags'=>"NVC\nGandhian Non-Violence\nInternational Coordination",'founder_image'=>'']];
}

function sk_get_default_art(): array {
    return [
        [
            'index' => 0,
            'num'   => '01',
            'title' => 'Art is available right now',
            'tag'   => 'Healing Art',
            'price' => '',
            'slug'  => 'contact',
            'desc'  => 'Our gallery is currently being curated. Please check back soon or enquire to learn more about our upcoming therapeutic art collections.',
            'img'   => '',
            'dimensions' => 'Various sizes',
            'medium' => 'Mixed media',
        ]
    ];
}

/**
 * Centralized query and transient cache for Art CPT.
 * Fetches all active art posts (up to 100) and caches the array.
 * Slices the array depending on the template requirements.
 */
function sk_get_art_data(int $count = 100): array {
    $art_posts_data = get_transient('sk_art_data');
    
    if (false === $art_posts_data) {
        $art_query = new WP_Query([
            'post_type'      => 'sk_art',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'no_found_rows'  => true,
        ]);

        $art_posts_data = [];
        if ($art_query->have_posts()) {
            while ($art_query->have_posts()) {
                $art_query->the_post();
                $pid = get_the_ID();
                $image_url = get_the_post_thumbnail_url($pid, 'full');
                if (!$image_url) {
                    $image_id = (int) get_post_meta($pid, 'art_image_id', true);
                    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : get_post_meta($pid, 'art_image', true);
                }
                $art_posts_data[] = [
                    'id'         => $pid,
                    'title'      => get_the_title(),
                    'permalink'  => get_permalink(),
                    'image_url'  => $image_url,
                    'tag'        => get_post_meta($pid, 'art_tag', true),
                ];
            }
            wp_reset_postdata();
        }
        
        if (empty($art_posts_data)) {
            $default_art = function_exists('sk_get_default_art') ? sk_get_default_art() : [];
            foreach ($default_art as $d) {
                $art_posts_data[] = [
                    'id'         => 0,
                    'title'      => $d['title'],
                    'permalink'  => home_url('/#contact'),
                    'image_url'  => $d['img'],
                    'tag'        => $d['tag'],
                ];
            }
        }
        
        set_transient('sk_art_data', $art_posts_data, 300); // 5 minute TTL for stability
    }
    
    return array_slice($art_posts_data, 0, $count);
}

/**
 * Cached wrapper for get_page_by_path to avoid raw SQL queries on every page render.
 * Caches the result in a transient for 1 day.
 */
function sk_get_page_id_by_path(string $path): int {
    $transient_key = 'sk_page_id_' . sanitize_key($path);
    $cached_id     = get_transient($transient_key);
    
    if ($cached_id !== false) {
        return (int) $cached_id;
    }
    
    $page = get_page_by_path($path);
    $page_id = $page ? (int) $page->ID : 0;
    
    set_transient($transient_key, $page_id, DAY_IN_SECONDS);
    
    return $page_id;
}

/**
 * AJAX Handler: Save FAQ Bubble X/Y Positions (Admin Only)
 */
add_action('wp_ajax_sk_save_bubble_positions', 'sk_save_bubble_positions_handler');
function sk_save_bubble_positions_handler(): void {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized permissions']);
    }
    $raw_positions = $_POST['positions'] ?? [];
    if (is_array($raw_positions) && !empty($raw_positions)) {
        $clean_positions = [];
        foreach ($raw_positions as $slug => $pos) {
            $clean_slug = sanitize_title($slug);
            if (!empty($clean_slug)) {
                $clean_positions[$clean_slug] = [
                    'x' => intval($pos['x'] ?? 0),
                    'y' => intval($pos['y'] ?? 0),
                ];
            }
        }
        // Force database option update
        delete_option('sk_faq_bubble_positions');
        $updated = update_option('sk_faq_bubble_positions', $clean_positions, true);
        wp_cache_delete('sk_faq_bubble_positions', 'options');
        wp_cache_flush();
        
        if (function_exists('litespeed_purge_all')) { litespeed_purge_all(); }
        if (function_exists('rocket_clean_domain')) { rocket_clean_domain(); }
        if (function_exists('w3tc_flush_all')) { w3tc_flush_all(); }
        if (function_exists('wp_cache_clear_cache')) { wp_cache_clear_cache(); }

        wp_send_json_success([
            'message'   => 'Saved successfully to database!',
            'updated'   => $updated,
            'positions' => $clean_positions
        ]);
    }
    wp_send_json_error(['message' => 'Empty position array received']);
}
