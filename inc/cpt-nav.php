<?php
/**
 * Sacred Kompass — Custom Post Type: Navigation Items
 */
defined('ABSPATH') || exit;

add_action('init', 'sk_register_nav_cpt', 10);
function sk_register_nav_cpt(): void {
    register_post_type('sk_nav', [
        'labels' => [
            'name'          => 'Navigation Items',
            'singular_name' => 'Nav Item',
            'add_new_item'  => 'Add Nav Item',
            'edit_item'     => 'Edit Nav Item',
            'menu_name'     => 'Navigation',
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => false,            // registered via sk_nest_nav_menu — prevents duplicate entry
        'supports'        => ['title', 'page-attributes'],
        'rewrite'         => false,
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => true,
        'menu_icon'       => 'dashicons-menu',
    ]);
}

add_action('admin_menu', 'sk_nest_nav_menu', 100);
function sk_nest_nav_menu(): void {
    add_submenu_page(
        'sk-settings',
        'Navigation Items',
        '☰ Navigation',
        'edit_posts',
        'edit.php?post_type=sk_nav'
    );
}

add_action('add_meta_boxes', 'sk_register_nav_meta_boxes');
function sk_register_nav_meta_boxes(): void {
    add_meta_box('sk_nav_details', '☰ Nav Item Settings', 'sk_nav_meta_box_cb', 'sk_nav', 'normal', 'high');
}

function sk_nav_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_nav_save', 'sk_nav_nonce');

    $url       = get_post_meta($post->ID, 'nav_url',       true) ?: '';
    $icon      = get_post_meta($post->ID, 'nav_icon',      true) ?: '';
    $highlight = get_post_meta($post->ID, 'nav_highlight', true) ?: 'none';
    $target    = get_post_meta($post->ID, 'nav_target',    true) ?: '_self';
    $mobile    = get_post_meta($post->ID, 'nav_show_mobile', true);
    $desktop   = get_post_meta($post->ID, 'nav_show_desktop', true);
    if ($mobile  === '') $mobile  = '1';
    if ($desktop === '') $desktop = '1';

    $highlight_opts = [
        'none'    => 'None (default link style)',
        'gold'    => 'Gold accent underline',
        'btn'     => 'Button (btn-primary)',
        'btn-ghost' => 'Button (ghost/outlined)',
    ];

    echo '<p style="font-size:12px;color:#666;margin:0 0 16px">Use <em>Page Attributes → Order</em> (right sidebar) to control menu order. Lower number = leftmost.</p>';
    echo '<table class="form-table" style="width:100%">';

    echo '<tr><th style="width:180px"><label>Label</label></th><td><p style="margin:0;color:#888;font-size:12px">Set via the <strong>Title</strong> field above (e.g. "About", "Our Services").</p></td></tr>';

    echo '<tr><th><label for="sk_nav_url">URL / Anchor</label></th>';
    echo '<td><input type="text" id="sk_nav_url" name="nav_url" value="' . esc_attr($url) . '" style="width:100%" placeholder="e.g. /#about or /journal/ or https://…" /></td></tr>';

    echo '<tr><th><label for="sk_nav_icon">Icon (optional)</label></th>';
    echo '<td><input type="text" id="sk_nav_icon" name="nav_icon" value="' . esc_attr($icon) . '" style="width:200px" placeholder="✦ or dashicon name" />';
    echo '<p style="margin:4px 0 0;font-size:11px;color:#888">Paste an emoji/symbol, or a <a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">Dashicon</a> class (e.g. <code>dashicons-heart</code>).</p></td></tr>';

    echo '<tr><th><label>Highlight Style</label></th><td>';
    foreach ($highlight_opts as $val => $label) {
        $checked = checked($highlight, $val, false);
        echo '<label style="display:inline-flex;align-items:center;gap:6px;margin-right:18px;cursor:pointer">';
        echo "<input type=\"radio\" name=\"nav_highlight\" value=\"{$val}\"{$checked} /> {$label}";
        echo '</label>';
    }
    echo '</td></tr>';

    echo '<tr><th><label>Open in</label></th><td>';
    echo '<label style="margin-right:16px"><input type="radio" name="nav_target" value="_self"' . checked($target,'_self',false) . '> Same tab</label>';
    echo '<label><input type="radio" name="nav_target" value="_blank"' . checked($target,'_blank',false) . '> New tab</label>';
    echo '</td></tr>';

    echo '<tr><th><label>Visibility</label></th><td style="display:flex;gap:20px;align-items:center">';
    echo '<label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="checkbox" name="nav_show_desktop" value="1"' . checked($desktop,'1',false) . ' /> Show on desktop</label>';
    echo '<label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="checkbox" name="nav_show_mobile"  value="1"' . checked($mobile,'1',false)  . ' /> Show on mobile</label>';
    echo '</td></tr>';

    echo '</table>';
    echo '<p style="margin-top:14px;font-size:12px;color:#888">💡 <strong>Tip:</strong> The "Contact Us" CTA button in the nav is managed separately via <em>Sacred Kompass → Settings → General</em>.</p>';
}

add_action('save_post_sk_nav', 'sk_save_nav_meta');
function sk_save_nav_meta(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sk_nav_nonce']) || !wp_verify_nonce($_POST['sk_nav_nonce'], 'sk_nav_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, 'nav_url',          sanitize_text_field($_POST['nav_url']       ?? ''));
    update_post_meta($post_id, 'nav_icon',         sanitize_text_field($_POST['nav_icon']      ?? ''));
    update_post_meta($post_id, 'nav_highlight',    sanitize_key(       $_POST['nav_highlight'] ?? 'none'));
    update_post_meta($post_id, 'nav_target',       in_array($_POST['nav_target'] ?? '', ['_self','_blank']) ? $_POST['nav_target'] : '_self');
    update_post_meta($post_id, 'nav_show_desktop', isset($_POST['nav_show_desktop']) ? '1' : '');
    update_post_meta($post_id, 'nav_show_mobile',  isset($_POST['nav_show_mobile'])  ? '1' : '');

    delete_transient('sk_nav_items');
    delete_transient('sk_nav_items_admin');
    delete_transient('sk_nav_items_public');
}

// Ensure transient is deleted when post is saved, trashed, untrashed, deleted, or reordered
add_action('save_post_sk_nav', 'sk_bust_nav_transients_on_edit', 999);
add_action('deleted_post', 'sk_bust_nav_transients_on_edit', 999);
add_action('trashed_post', 'sk_bust_nav_transients_on_edit', 999);
add_action('untrashed_post', 'sk_bust_nav_transients_on_edit', 999);
function sk_bust_nav_transients_on_edit($post_id): void {
    if (get_post_type($post_id) === 'sk_nav') {
        delete_transient('sk_nav_items');
        delete_transient('sk_nav_items_admin');
        delete_transient('sk_nav_items_public');
    }
}

function sk_get_nav_items(): array {
    // Delete transients to ensure fresh section order on every page load
    delete_transient('sk_nav_items');
    delete_transient('sk_nav_items_admin');
    delete_transient('sk_nav_items_public');

    $section_map     = function_exists('sk_nav_section_map') ? sk_nav_section_map() : [];
    $section_enabled = function(string $url) use ($section_map): bool {
        // Parse target URL to compare path and fragment relatively
        $parts = wp_parse_url($url);
        $path = isset($parts['path']) ? rtrim($parts['path'], '/') : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
        $normalized_rel = ($path === '/' || $path === '') ? $fragment : $path . $fragment;
        if ($normalized_rel === '') {
            $normalized_rel = '#';
        }

        foreach ($section_map as $anchor => $key) {
            // Parse anchor URL to compare path and fragment relatively
            $anchor_parts = wp_parse_url($anchor);
            $anchor_path = isset($anchor_parts['path']) ? rtrim($anchor_parts['path'], '/') : '';
            $anchor_frag = isset($anchor_parts['fragment']) ? '#' . $anchor_parts['fragment'] : '';
            $normalized_anchor = ($anchor_path === '/' || $anchor_path === '') ? $anchor_frag : $anchor_path . $anchor_frag;
            if ($normalized_anchor === '') {
                $normalized_anchor = '#';
            }

            // Match if normalized relative paths match, or exact strings match, or home_url replacement matches
            if ($normalized_rel === $normalized_anchor || $url === $anchor || str_replace(rtrim(home_url(), '/'), '', $url) === $anchor) {
                if (function_exists('sk_section_enabled') && !sk_section_enabled($key)) {
                    return false;
                }
                if (function_exists('sk_section_admin_only') && sk_section_admin_only($key) && !current_user_can('edit_posts')) {
                    return false;
                }
            }
        }
        return true;
    };

    $posts = get_posts([
        'post_type'      => 'sk_nav',
        'post_status'    => 'publish',
        'posts_per_page' => 50,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ]);

    if (empty($posts)) {
        return [];
    }

    $items = [];
    foreach ($posts as $p) {
        $url = get_post_meta($p->ID, 'nav_url', true) ?: '#';
        if (!$section_enabled($url)) continue;
        $items[] = [
            'label'      => get_the_title($p),
            'url'        => $url,
            'highlight'  => get_post_meta($p->ID, 'nav_highlight',    true) ?: 'none',
            'target'     => get_post_meta($p->ID, 'nav_target',       true) ?: '_self',
            'desktop'    => (bool)(get_post_meta($p->ID, 'nav_show_desktop', true) !== ''),
            'mobile'     => (bool)(get_post_meta($p->ID, 'nav_show_mobile',  true) !== ''),
            'icon'       => get_post_meta($p->ID, 'nav_icon',         true) ?: '',
            'menu_order' => $p->menu_order,
        ];
    }

    // Automatically reorder navigation items to match the homepage section render order
    if (!empty($items)) {
        $get_item_section = function(array $item) use ($section_map): ?string {
            $url   = strtolower(trim($item['url'] ?? ''));
            $label = strtolower(trim($item['label'] ?? ''));

            // 1. Check direct section_map anchors
            foreach ($section_map as $anchor => $key) {
                $clean_anchor = strtolower($anchor);
                if (str_ends_with($url, $clean_anchor) || $url === $clean_anchor || str_contains($url, $clean_anchor)) {
                    return $key;
                }
            }

            // 2. Keyword fallback on label or URL
            if (str_contains($label, 'about') || str_contains($url, 'about')) return 'about';
            if (str_contains($label, 'art') || str_contains($url, 'art')) return 'art';
            if (str_contains($label, 'philosoph') || str_contains($url, 'philosoph')) return 'philosophy';
            if (str_contains($label, 'founder') || str_contains($label, 'collective') || str_contains($url, 'collective') || str_contains($url, 'founders')) return 'founders';
            if (str_contains($label, 'story') || str_contains($label, 'stories') || str_contains($url, 'stories')) return 'stories_preview';
            if (str_contains($label, 'journal') || str_contains($url, 'journal')) return 'journal';
            if (str_contains($label, 'faq') || str_contains($label, 'question') || str_contains($url, 'faq')) return 'faq';
            if (str_contains($label, 'contact') || str_contains($label, 'connect') || str_contains($url, 'contact')) return 'cta';

            return null;
        };

        $section_order = function_exists('sk_get_section_render_order') ? sk_get_section_render_order() : [];

        usort($items, function($a, $b) use ($get_item_section, $section_order) {
            $sec_a = $get_item_section($a);
            $sec_b = $get_item_section($b);

            $weight_a = 2000;
            if ($sec_a !== null) {
                $idx = array_search($sec_a, $section_order, true);
                $weight_a = ($idx !== false) ? $idx : 1000;
            }

            $weight_b = 2000;
            if ($sec_b !== null) {
                $idx = array_search($sec_b, $section_order, true);
                $weight_b = ($idx !== false) ? $idx : 1000;
            }

            if ($weight_a !== $weight_b) {
                return $weight_a <=> $weight_b;
            }

            return $a['menu_order'] <=> $b['menu_order'];
        });
    }

    return $items;
}
