<?php
/**
 * Sacred Kompass — Custom Post Type: Announcement Banners
 */
defined('ABSPATH') || exit;

add_action('init', 'sk_register_announcement_cpt', 10);
function sk_register_announcement_cpt(): void {
    register_post_type('sk_announcement', [
        'labels' => [
            'name'          => 'Announcements',
            'singular_name' => 'Announcement',
            'add_new_item'  => 'Add New Announcement',
            'edit_item'     => 'Edit Announcement',
            'menu_name'     => 'Announcements',
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => false,
        'supports'        => ['title'],
        'rewrite'         => false,
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => false,
        'menu_icon'       => 'dashicons-megaphone',
    ]);
}

add_action('admin_menu', 'sk_nest_announcement_menu', 100);
function sk_nest_announcement_menu(): void {
    add_submenu_page(
        'sk-settings',
        'Announcements',
        '📢 Announcements',
        'edit_posts',
        'edit.php?post_type=sk_announcement'
    );
}

add_action('add_meta_boxes', 'sk_register_announcement_meta_boxes');
function sk_register_announcement_meta_boxes(): void {
    add_meta_box('sk_announcement_details', '📢 Announcement Bar Settings', 'sk_announcement_meta_box_cb', 'sk_announcement', 'normal', 'high');
}

function sk_announcement_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_announcement_save', 'sk_announcement_nonce');
    $message     = get_post_meta($post->ID, 'ann_message',    true);
    $subtitle    = get_post_meta($post->ID, 'ann_subtitle',   true);
    $cta_text    = get_post_meta($post->ID, 'ann_cta_text',   true);
    $cta_url     = get_post_meta($post->ID, 'ann_cta_url',    true);
    $bg_color    = get_post_meta($post->ID, 'ann_bg_color',   true) ?: '#2c3e2d';
    $text_color  = get_post_meta($post->ID, 'ann_text_color', true) ?: '#f5f0e8';
    $dismissible = (bool) get_post_meta($post->ID, 'ann_dismissible', true);
    $countdown_end = get_post_meta($post->ID, 'ann_countdown_end', true);
    ?>
    <p style="font-size:12px;color:#666;margin:0 0 14px">
      The <strong>post title</strong> is your internal label (e.g. "June Retreat Promo"). Set status to <strong>Published</strong> to show the bar; <strong>Draft</strong> to hide it without deleting.
      Only the <strong>most recently published</strong> announcement shows. The bar appears above the nav on every page.
    </p>
    <table class="form-table" style="width:100%">
      <tr><th style="width:180px"><label>Main Message</label></th>
          <td><input type="text" name="ann_message" value="<?php echo esc_attr($message); ?>" style="width:100%" placeholder="e.g. 🔥 Flash Sale: June Retreat Special!" />
          <p class="description" style="margin-top:4px;font-size:11px">Emoji supported. Shown prominently. Keep under 80 characters.</p></td></tr>
      <tr><th><label>Subtitle / Detail</label></th>
          <td><input type="text" name="ann_subtitle" value="<?php echo esc_attr($subtitle); ?>" style="width:100%" placeholder="e.g. Get up to 30% off all retreats. Limited spaces available." />
          <p class="description" style="margin-top:4px;font-size:11px">Optional. Smaller line below the main message.</p></td></tr>
      <tr><th><label>CTA Button Text</label></th>
          <td><input type="text" name="ann_cta_text" value="<?php echo esc_attr($cta_text); ?>" style="width:260px" placeholder="e.g. Book Now (optional)" /></td></tr>
      <tr><th><label>CTA Button URL</label></th>
          <td><input type="url" name="ann_cta_url" value="<?php echo esc_attr($cta_url); ?>" style="width:100%" placeholder="https://… or /#contact" /></td></tr>
      <tr><th><label>Countdown Timer Ends</label></th>
          <td><input type="datetime-local" name="ann_countdown_end" value="<?php echo esc_attr($countdown_end); ?>" style="width:260px" />
          <p class="description" style="margin-top:4px;font-size:11px">Optional. When set, a live countdown timer appears in the bar. Leave blank to hide the timer. The bar hides automatically when the countdown expires.</p></td></tr>
      <tr><td colspan="2"><hr style="margin:8px 0;border:none;border-top:1px solid #f0f0f1"></td></tr>
      <tr><th><label>Background Colour</label></th>
          <td><div style="display:flex;align-items:center;gap:10px">
            <input type="color" name="ann_bg_color" value="<?php echo esc_attr($bg_color); ?>" style="width:48px;height:34px;border:1px solid #ddd;border-radius:4px;cursor:pointer" />
            <input type="text" name="ann_bg_color_hex" value="<?php echo esc_attr($bg_color); ?>" style="width:110px" placeholder="#2c3e2d" oninput="document.querySelector('[name=ann_bg_color]').value=this.value" />
          </div>
          <p class="description" style="margin-top:4px;font-size:11px">Default: dark forest green (#2c3e2d).</p></td></tr>
      <tr><th><label>Text Colour</label></th>
          <td><div style="display:flex;align-items:center;gap:10px">
            <input type="color" name="ann_text_color" value="<?php echo esc_attr($text_color); ?>" style="width:48px;height:34px;border:1px solid #ddd;border-radius:4px;cursor:pointer" />
            <input type="text" name="ann_text_color_hex" value="<?php echo esc_attr($text_color); ?>" style="width:110px" placeholder="#f5f0e8" oninput="document.querySelector('[name=ann_text_color]').value=this.value" />
          </div></td></tr>
      <tr><th><label>Dismissible</label></th>
          <td><label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="ann_dismissible" value="1"<?php checked($dismissible); ?>>
            Show a close (×) button so visitors can dismiss the bar for their session</label></td></tr>
    </table>
    <div style="margin-top:16px;padding:12px 16px;border-radius:4px;background:<?php echo esc_attr($bg_color); ?>;color:<?php echo esc_attr($text_color); ?>;font-size:13px;display:flex;align-items:center;justify-content:space-between;gap:12px" id="sk-ann-preview">
      <span id="sk-ann-preview-msg"><?php echo esc_html($message ?: 'Your announcement will appear here'); ?></span>
      <?php if ($cta_text): ?>
      <span style="border:1px solid currentColor;border-radius:3px;padding:3px 10px;white-space:nowrap;font-size:12px"><?php echo esc_html($cta_text); ?></span>
      <?php endif; ?>
    </div>
    <p style="font-size:11px;color:#888;margin-top:6px">Preview above updates on save.</p>
    <?php
}

add_action('save_post_sk_announcement', 'sk_save_announcement_meta');
function sk_save_announcement_meta(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sk_announcement_nonce']) || !wp_verify_nonce($_POST['sk_announcement_nonce'], 'sk_announcement_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, 'ann_message',    sanitize_text_field($_POST['ann_message']    ?? ''));
    update_post_meta($post_id, 'ann_subtitle',   sanitize_text_field($_POST['ann_subtitle']   ?? ''));
    update_post_meta($post_id, 'ann_cta_text',   sanitize_text_field($_POST['ann_cta_text']   ?? ''));
    update_post_meta($post_id, 'ann_cta_url',    esc_url_raw($_POST['ann_cta_url']            ?? ''));
    
    $bg = preg_match('/^#[0-9a-fA-F]{3,6}$/', $_POST['ann_bg_color'] ?? '') ? $_POST['ann_bg_color'] : '#2c3e2d';
    $fg = preg_match('/^#[0-9a-fA-F]{3,6}$/', $_POST['ann_text_color'] ?? '') ? $_POST['ann_text_color'] : '#f5f0e8';
    update_post_meta($post_id, 'ann_bg_color',   $bg);
    update_post_meta($post_id, 'ann_text_color', $fg);
    update_post_meta($post_id, 'ann_dismissible', isset($_POST['ann_dismissible']) ? '1' : '');
    
    $countdown_raw = sanitize_text_field($_POST['ann_countdown_end'] ?? '');
    $countdown_end = (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $countdown_raw)) ? $countdown_raw : '';
    update_post_meta($post_id, 'ann_countdown_end', $countdown_end);

    delete_transient('sk_active_announcement');
}

add_action('transition_post_status', 'sk_bust_announcement_transient_on_status', 10, 3);
function sk_bust_announcement_transient_on_status(string $new, string $old, WP_Post $post): void {
    if ($post->post_type === 'sk_announcement') {
        delete_transient('sk_active_announcement');
    }
}

add_action('before_delete_post', 'sk_bust_announcement_on_delete');
add_action('wp_trash_post',      'sk_bust_announcement_on_delete');
function sk_bust_announcement_on_delete(int $post_id): void {
    if (get_post_type($post_id) === 'sk_announcement') {
        delete_transient('sk_active_announcement');
    }
}

function sk_get_active_announcement(): ?array {
    $cached = get_transient('sk_active_announcement');
    if ($cached !== false) return ($cached === [] || $cached === '') ? null : $cached;

    $posts = get_posts([
        'post_type'      => 'sk_announcement',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ]);

    if (empty($posts)) {
        set_transient('sk_active_announcement', [], 1 * MINUTE_IN_SECONDS);
        return null;
    }

    $p   = $posts[0];
    $msg = get_post_meta($p->ID, 'ann_message', true);
    if (empty($msg)) {
        set_transient('sk_active_announcement', [], 1 * MINUTE_IN_SECONDS);
        return null;
    }

    $ann = [
        'message'      => $msg,
        'subtitle'     => get_post_meta($p->ID, 'ann_subtitle',     true),
        'cta_text'     => get_post_meta($p->ID, 'ann_cta_text',     true),
        'cta_url'      => get_post_meta($p->ID, 'ann_cta_url',      true),
        'bg_color'     => get_post_meta($p->ID, 'ann_bg_color',     true) ?: '#2c3e2d',
        'text_color'   => get_post_meta($p->ID, 'ann_text_color',   true) ?: '#f5f0e8',
        'dismissible'  => (bool) get_post_meta($p->ID, 'ann_dismissible',  true),
        'countdown_end'=> get_post_meta($p->ID, 'ann_countdown_end', true),
        'id'           => $p->ID,
    ];

    set_transient('sk_active_announcement', $ann, 5 * MINUTE_IN_SECONDS);
    return $ann;
}
