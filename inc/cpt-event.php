<?php
/**
 * Sacred Kompass — Custom Post Type: Events
 */
defined('ABSPATH') || exit;

add_action('init', 'sk_register_event_cpt', 10);
function sk_register_event_cpt(): void {
    register_post_type('sk_event', [
        'labels' => [
            'name'          => 'Events',
            'singular_name' => 'Event',
            'add_new_item'  => 'Add New Event',
            'edit_item'     => 'Edit Event',
            'menu_name'     => 'Events',
        ],
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => false,
        'supports'        => ['title', 'thumbnail', 'page-attributes'],
        'rewrite'         => ['slug' => 'events'],
        'capability_type' => 'post',
        'has_archive'     => true,
        'show_in_rest'    => true,
        'menu_icon'       => 'dashicons-calendar-alt',
    ]);
}

add_action('admin_menu', 'sk_nest_event_menu', 100);
function sk_nest_event_menu(): void {
    add_submenu_page(
        'sk-settings',
        'Events',
        '✦ Events',
        'edit_posts',
        'edit.php?post_type=sk_event'
    );
}

add_action('add_meta_boxes', 'sk_register_event_meta_boxes');
function sk_register_event_meta_boxes(): void {
    add_meta_box('sk_event_details', '★ Event Details', 'sk_event_meta_box_cb', 'sk_event', 'normal', 'high');
}

function sk_event_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_event_save', 'sk_event_nonce');
    $date         = get_post_meta($post->ID, 'event_date',         true);
    $time         = get_post_meta($post->ID, 'event_time',         true);
    $end_time     = get_post_meta($post->ID, 'event_end_time',     true);
    $location     = get_post_meta($post->ID, 'event_location',     true);
    $location_url = get_post_meta($post->ID, 'event_location_url', true);
    $format       = get_post_meta($post->ID, 'event_format',       true) ?: 'inperson';
    $zoom_url     = get_post_meta($post->ID, 'event_zoom_url',     true);
    $capacity     = get_post_meta($post->ID, 'event_capacity',     true);
    $price        = get_post_meta($post->ID, 'event_price',        true);
    $reg_url      = get_post_meta($post->ID, 'event_reg_url',      true);
    $description  = get_post_meta($post->ID, 'event_description',  true);
    $tag          = get_post_meta($post->ID, 'event_tag',          true);
    $sold_out     = (bool) get_post_meta($post->ID, 'event_sold_out', true);

    $format_opts = ['inperson' => 'In-person', 'online' => 'Online', 'hybrid' => 'Hybrid'];
    ?>
    <table class="form-table" style="width:100%">
      <tr><th style="width:180px"><label>Event Tag / Category</label></th>
          <td><input type="text" name="event_tag" value="<?php echo esc_attr($tag); ?>" style="width:260px" placeholder="e.g. Workshop · Retreat · Masterclass" /></td></tr>
      <tr><th><label>Short Description</label></th>
          <td><textarea name="event_description" rows="3" style="width:100%" placeholder="One or two lines shown on the event card."><?php echo esc_textarea($description); ?></textarea></td></tr>
      <tr><td colspan="2"><hr style="margin:8px 0;border:none;border-top:1px solid #f0f0f1"></td></tr>
      <tr><th><label>Date</label></th>
          <td><input type="date" name="event_date" value="<?php echo esc_attr($date); ?>" style="width:200px" /></td></tr>
      <tr><th><label>Start Time</label></th>
          <td><input type="time" name="event_time" value="<?php echo esc_attr($time); ?>" style="width:140px" /></td></tr>
      <tr><th><label>End Time</label></th>
          <td><input type="time" name="event_end_time" value="<?php echo esc_attr($end_time); ?>" style="width:140px" />
          <p class="description" style="margin-top:4px;font-size:11px">Optional — leave blank to show start time only.</p></td></tr>
      <tr><td colspan="2"><hr style="margin:8px 0;border:none;border-top:1px solid #f0f0f1"></td></tr>
      <tr><th><label>Format</label></th>
          <td><?php foreach ($format_opts as $val => $lbl): $chk = checked($format, $val, false); ?>
          <label style="display:inline-flex;align-items:center;gap:5px;margin-right:18px;cursor:pointer">
            <input type="radio" name="event_format" value="<?php echo esc_attr($val); ?>"<?php echo $chk; ?>> <?php echo esc_html($lbl); ?>
          </label><?php endforeach; ?></td></tr>
      <tr><th><label>Venue / Location</label></th>
          <td><input type="text" name="event_location" value="<?php echo esc_attr($location); ?>" style="width:100%" placeholder="e.g. The Hive, Carpenter Street, Singapore" /></td></tr>
      <tr><th><label>Google Maps URL</label></th>
          <td><input type="url" name="event_location_url" value="<?php echo esc_attr($location_url); ?>" style="width:100%" placeholder="https://maps.google.com/… (optional)" /></td></tr>
      <tr><th><label>Zoom / Online Link</label></th>
          <td><input type="url" name="event_zoom_url" value="<?php echo esc_attr($zoom_url); ?>" style="width:100%" placeholder="https://zoom.us/j/… — shown only for Online / Hybrid" /></td></tr>
      <tr><td colspan="2"><hr style="margin:8px 0;border:none;border-top:1px solid #f0f0f1"></td></tr>
      <tr><th><label>Capacity</label></th>
          <td><input type="text" name="event_capacity" value="<?php echo esc_attr($capacity); ?>" style="width:200px" placeholder="e.g. 20 seats · Unlimited · 8 spots" /></td></tr>
      <tr><th><label>Price</label></th>
          <td><input type="text" name="event_price" value="<?php echo esc_attr($price); ?>" style="width:200px" placeholder="e.g. SGD 120 · Free · From SGD 80" /></td></tr>
      <tr><th><label>Registration URL</label></th>
          <td><input type="url" name="event_reg_url" value="<?php echo esc_attr($reg_url); ?>" style="width:100%" placeholder="https://… or /#contact for the enquiry form" /></td></tr>
      <tr><th><label>Sold Out</label></th>
          <td><label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="event_sold_out" value="1"<?php checked($sold_out); ?>>
            Mark as sold out (replaces register button with "Sold Out" badge)</label></td></tr>
    </table>
    <p style="font-size:12px;color:#666;margin-top:12px">💡 <strong>Tip:</strong> Past events (date before today) are automatically hidden from the homepage widget. They stay visible on the /events archive page for reference.</p>
    <?php
}

add_action('save_post_sk_event', 'sk_save_event_meta');
function sk_save_event_meta(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sk_event_nonce']) || !wp_verify_nonce($_POST['sk_event_nonce'], 'sk_event_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, 'event_tag',          sanitize_text_field($_POST['event_tag']         ?? ''));
    update_post_meta($post_id, 'event_description',  sanitize_textarea_field($_POST['event_description'] ?? ''));
    update_post_meta($post_id, 'event_date',         sanitize_text_field($_POST['event_date']         ?? ''));
    update_post_meta($post_id, 'event_time',         sanitize_text_field($_POST['event_time']         ?? ''));
    update_post_meta($post_id, 'event_end_time',     sanitize_text_field($_POST['event_end_time']     ?? ''));
    
    $fmt_allowed = ['inperson', 'online', 'hybrid'];
    $fmt = $_POST['event_format'] ?? 'inperson';
    update_post_meta($post_id, 'event_format',       in_array($fmt, $fmt_allowed) ? $fmt : 'inperson');
    update_post_meta($post_id, 'event_location',     sanitize_text_field($_POST['event_location']     ?? ''));
    update_post_meta($post_id, 'event_location_url', esc_url_raw($_POST['event_location_url']         ?? ''));
    update_post_meta($post_id, 'event_zoom_url',     esc_url_raw($_POST['event_zoom_url']             ?? ''));
    update_post_meta($post_id, 'event_capacity',     sanitize_text_field($_POST['event_capacity']     ?? ''));
    update_post_meta($post_id, 'event_price',        sanitize_text_field($_POST['event_price']        ?? ''));
    update_post_meta($post_id, 'event_reg_url',      esc_url_raw($_POST['event_reg_url']              ?? ''));
    update_post_meta($post_id, 'event_sold_out',     isset($_POST['event_sold_out']) ? '1' : '');
}

function sk_get_upcoming_events(int $limit = 3): array {
    $today = date('Y-m-d');
    $posts = get_posts([
        'post_type'      => 'sk_event',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'orderby'        => 'meta_value',
        'meta_key'       => 'event_date',
        'order'          => 'ASC',
        'meta_query'     => [['key' => 'event_date', 'value' => $today, 'compare' => '>=', 'type' => 'DATE']],
        'no_found_rows'  => true,
    ]);

    $items = [];
    foreach ($posts as $p) {
        $date_raw = get_post_meta($p->ID, 'event_date', true);
        $items[] = [
            'title'        => get_the_title($p),
            'tag'          => get_post_meta($p->ID, 'event_tag',         true),
            'description'  => get_post_meta($p->ID, 'event_description', true),
            'date'         => $date_raw ? date_i18n('j F Y', strtotime($date_raw)) : '',
            'date_day'     => $date_raw ? date_i18n('j', strtotime($date_raw)) : '',
            'date_month'   => $date_raw ? date_i18n('M', strtotime($date_raw)) : '',
            'time'         => get_post_meta($p->ID, 'event_time',        true),
            'end_time'     => get_post_meta($p->ID, 'event_end_time',    true),
            'format'       => get_post_meta($p->ID, 'event_format',      true) ?: 'inperson',
            'location'     => get_post_meta($p->ID, 'event_location',    true),
            'price'        => get_post_meta($p->ID, 'event_price',       true),
            'reg_url'      => get_post_meta($p->ID, 'event_reg_url',     true) ?: home_url('/#contact'),
            'sold_out'     => (bool) get_post_meta($p->ID, 'event_sold_out', true),
            'img'          => get_the_post_thumbnail_url($p->ID, 'medium') ?: '',
        ];
    }
    return $items;
}
