<?php
// Load WordPress bootstrap
define('WP_USE_THEMES', false);
require_once 'C:/Users/sahil/Local Sites/sacredkompass/app/public/wp-load.php';

// Get custom sections
$custom_sections = get_option('sk_custom_sections', '[]');
echo "CUSTOM SECTIONS:\n";
echo $custom_sections . "\n\n";

// Get active homepage section order
$section_order = get_option('sk_section_order', '');
echo "SECTION ORDER:\n";
echo $section_order . "\n\n";

// Get all CPT post counts
$post_types = ['post', 'page', 'sk_event', 'sk_story', 'sk_puja', 'sk_art', 'sk_faq', 'sk_team'];
echo "POST TYPES AND COUNTS:\n";
foreach ($post_types as $pt) {
    $count = wp_count_posts($pt);
    echo "$pt: " . ($count->publish ?? 0) . " published\n";
}
