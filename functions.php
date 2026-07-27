<?php
/**
 * Sacred Kompass — functions.php (Modularized)
 *
 * KEY CHANGE: Zero DB-heavy hooks on frontend init.
 * Modular architecture splitting settings, CPTs, SEO, and helpers.
 */
defined('ABSPATH') || exit;

// 1. Core Helpers and Utility Functions
require_once __DIR__ . '/inc/helpers.php';

// 2. Setup, Styles, and Scripts registration
require_once __DIR__ . '/inc/setup.php';

// 3. Search Engine Optimization & Structured Schema
require_once __DIR__ . '/inc/seo.php';

// 4. AJAX Form Handler & Lead Database Table Creator
require_once __DIR__ . '/inc/ajax-contact.php';
require_once __DIR__ . '/inc/webhook-proxy.php';

// 5. Custom Post Types & Components
require_once __DIR__ . '/inc/cpt-art.php';
require_once __DIR__ . '/inc/cpt-faq.php';
require_once __DIR__ . '/inc/cpt-team.php';
require_once __DIR__ . '/inc/cpt-legal.php';
require_once __DIR__ . '/inc/cpt-nav.php';
require_once __DIR__ . '/inc/cpt-event.php';
require_once __DIR__ . '/inc/cpt-announcement.php';
require_once __DIR__ . '/inc/cpt-story.php';

// 6. Administration dashboards
require_once __DIR__ . '/inc/admin-settings.php';
require_once __DIR__ . '/inc/admin-sections.php';

// 7. Default Database Content Seeder
require_once __DIR__ . '/inc/content.php';

// 8. Advanced Custom Fields (ACF) local field groups
require_once __DIR__ . '/inc/acf-fields.php';

/* ── CONSOLIDATED REWRITE FLUSH GUARD ── */
add_action('init', function(): void {
    if (get_option('sk_theme_rewrite_flush_v22') === '1') return;

    if (is_admin()) {
        sk_ensure_stories_page();
    }

    flush_rewrite_rules(false);
    update_option('sk_theme_rewrite_flush_v22', '1', false);
}, 99);

/* ── PLAIN-PERMALINK BLOG ROUTING ───────
 * Redirect old /blog/ URLs → /journal/ so any saved links don't 404
 * ─────────────────────────────────────── */
add_action('template_redirect', function(): void {
    if (!get_option('permalink_structure')) return;
    $path = parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ?? '';
    if ( rtrim($path, '/') === '/blog' ) {
        wp_redirect( home_url( '/journal/' ), 301 );
        exit;
    }
});

add_filter('redirect_canonical', function($redirect_url, $requested_url) {
    $path = parse_url((string) $requested_url, PHP_URL_PATH) ?? '';
    $path = rtrim($path, '/');

    if ($path === '/terms' || $path === '/terms-of-use') {
        return false;
    }

    return $redirect_url;
}, 10, 2);

/* ── ACTIVATION & LIFECYCLE SEEDING ── */
add_action('after_switch_theme', 'sk_on_activation');
function sk_on_activation(): void {
    sk_create_default_pages();
    sk_seed_acf_options();
    sk_migrate_about_options();
    sk_seed_json_defaults();

    sk_create_leads_table();
    flush_rewrite_rules();
}

/**
 * Create default pages and set up reading settings
 */
function sk_create_default_pages(): void {
    $pages = [
        'home'           => 'Home',
        'journal'        => 'Journal',
        'collective'     => 'The Collective',
        'art-for-peace'  => 'Art for Peace',
        'stories'        => 'Stories',
        'privacy-policy' => 'Privacy Policy',
        'terms-of-use'   => 'Terms of Use',
        'disclaimer'     => 'Disclaimer'
    ];
    $home_id = 0; $journal_id = 0;
    foreach ($pages as $slug => $title) {
        $existing = get_page_by_path($slug);
        if (!$existing) {
            $id = wp_insert_post(['post_title'=>$title,'post_name'=>$slug,'post_status'=>'publish','post_type'=>'page','post_content'=>'']);
            if (!is_wp_error($id)) {
                if ($slug==='home') $home_id=$id;
                if ($slug==='journal') $journal_id=$id;
                if ($slug==='collective')    update_post_meta($id, '_wp_page_template', 'page-collective.php');
                if ($slug==='art-for-peace') update_post_meta($id, '_wp_page_template', 'page-art.php');
                if ($slug==='stories')       update_post_meta($id, '_wp_page_template', 'page-stories.php');
            }
        } else {
            if ($slug==='home') $home_id=$existing->ID;
            if ($slug==='journal') $journal_id=$existing->ID;
            if ($slug==='collective' && get_post_meta($existing->ID,'_wp_page_template',true) !== 'page-collective.php') {
                update_post_meta($existing->ID, '_wp_page_template', 'page-collective.php');
            }
            if ($slug==='art-for-peace' && get_post_meta($existing->ID,'_wp_page_template',true) !== 'page-art.php') {
                update_post_meta($existing->ID, '_wp_page_template', 'page-art.php');
            }
            if ($slug==='stories' && get_post_meta($existing->ID,'_wp_page_template',true) !== 'page-stories.php') {
                update_post_meta($existing->ID, '_wp_page_template', 'page-stories.php');
            }
        }
    }
    if ($home_id) { update_option('show_on_front','page'); update_option('page_on_front',$home_id); }
    if ($journal_id) { update_option('page_for_posts',$journal_id); }
}

/**
 * Seed theme settings defaults into ACF option keys
 */
function sk_seed_acf_options(): void {
    foreach (sk_acf_defaults() as $key => $val) {
        if (get_option('options_'.$key) === false) update_option('options_'.$key, $val, false);
    }
}

/**
 * Migrate legacy v13 theme properties to the v20 format
 */
function sk_migrate_about_options(): void {
    if (get_option('sk_about_v13_migrated') !== 'v20') {
        $new_about = [
            'options_sk_hero_eyebrow'             => 'Sacred Kompass · Transformation',
            'options_sk_hero_label_from'           => 'from',
            'options_sk_hero_label_to'             => 'to',
            'options_sk_about_eyebrow'             => 'Our Services',
            'options_sk_about_confluence_heading'  => 'A space for confluence of minds to reach peace',
            'options_sk_about_expression_line'     => "An expression for\nconsciousness and transformation",
            'options_sk_about_seo_body'            => 'Sacred Kompass Collective bridges ancient wisdom and modern living through Indian Astrology, Meditative Journeys, and Events on Well-being — guiding individuals and organisations toward inner clarity, conscious leadership, and lasting transformation.',
            'options_sk_about_traditions'          => "Indian Astrology\nMeditative Journeys\nEvents on Well-being",
            'options_sk_about_traditions_label'    => 'Paths we walk together',
        ];
        foreach ($new_about as $k => $v) {
            if (get_option($k) === false) update_option($k, $v, false);
        }
        update_option('sk_about_v13_migrated', 'v20', false);
    }
}

/**
 * Seed default JSON structures for founders and pillars
 */
function sk_seed_json_defaults(): void {
    if (!get_option('options_sk_philosophy_pillars_json')) update_option('options_sk_philosophy_pillars_json',wp_json_encode(sk_default_pillars()),false);
    if (!get_option('options_sk_founders_json'))           update_option('options_sk_founders_json',wp_json_encode(sk_default_founders()),false);
}



function sk_ensure_stories_page() {
    $existing = get_page_by_path( 'stories' );
    if ( ! $existing ) {
        $id = wp_insert_post( [
            'post_title'  => 'Stories',
            'post_name'   => 'stories',
            'post_status' => 'publish',
            'post_type'   => 'page',
        ] );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_wp_page_template', 'page-stories.php' );
        }
    } else {
        update_post_meta( $existing->ID, '_wp_page_template', 'page-stories.php' );
        if ( $existing->post_status !== 'publish' ) {
            wp_update_post( [ 'ID' => $existing->ID, 'post_status' => 'publish' ] );
        }
    }
}
