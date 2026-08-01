<?php
/**
 * Sacred Kompass — Homepage template
 *
 * Dynamic section render — controlled via Appearance > Homepage Sections
 * Order, visibility, and custom sections are all managed from the admin panel.
 */
get_header();

echo '<main id="main-content" role="main">' . "\n";

$builtin     = sk_builtin_sections();   // key → [ label, template ]
$custom_raw  = get_option( 'sk_custom_sections', '[]' );
$custom_list = json_decode( $custom_raw, true ) ?: [];
$custom_map  = [];
foreach ( $custom_list as $c ) { $custom_map[ $c['id'] ] = $c; }

$render_order = sk_get_section_render_order();

foreach ( $render_order as $key ) {
    if ( str_starts_with( $key, 'custom_' ) ) {
        // Custom HTML section
        $cid = substr( $key, 7 );
        if ( isset( $custom_map[ $cid ] ) && ! empty( $custom_map[ $cid ]['enabled'] ) ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitised on save via wp_kses_post
            echo $custom_map[ $cid ]['content'];
        }
    } else {
        // Built-in section
        if ( ! isset( $builtin[ $key ] ) ) continue;
        $sk_show_val = get_option( 'sk_show_' . $key, null );
        if ( $sk_show_val !== null && ! (bool) $sk_show_val ) continue;
        // Admin-only: skip for public visitors
        if ( (bool) get_option( 'sk_admin_only_' . $key, false ) && ! current_user_can( 'edit_posts' ) ) continue;
        get_template_part( $builtin[ $key ]['template'] );
    }
}

echo '</main>' . "\n";

get_footer();
