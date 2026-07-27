<?php
/**
 * Sacred Kompass — Custom Post Type: Puja
 */
defined('ABSPATH') || exit;

add_action('init', 'sk_register_puja_cpt', 10);
function sk_register_puja_cpt(): void {
    register_post_type('sk_puja', [
        'labels' => ['name'=>'Pujas','singular_name'=>'Puja Item','add_new_item'=>'Add New Puja Item','edit_item'=>'Edit Puja Item'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => false,
        'supports'        => ['title','page-attributes'],
        'rewrite'         => false,
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => true,
    ]);
}

add_action('admin_menu', 'sk_nest_puja_menu', 100);
function sk_nest_puja_menu(): void {
    add_submenu_page(
        'sk-settings',
        'Pujas',
        '✦ Kerala Pujas',
        'edit_posts',
        'edit.php?post_type=sk_puja'
    );
}

add_action('add_meta_boxes', 'sk_register_puja_meta_boxes');
function sk_register_puja_meta_boxes(): void {
    add_meta_box('sk_puja_details', 'Puja Details', 'sk_puja_meta_box', 'sk_puja', 'normal', 'high');
}

function sk_puja_meta_box(WP_Post $post): void {
    wp_nonce_field('sk_puja_save', 'sk_puja_nonce');
    $desc = get_post_meta($post->ID, 'puja_desc', true);
    $num  = get_post_meta($post->ID, 'puja_num',  true);
    ?>
    <table class="form-table" style="width:100%">
      <tr><th style="width:180px"><label>Display Numeral (Optional)</label></th>
          <td><input type="text" name="puja_num" value="<?php echo esc_attr($num); ?>" style="width:120px" placeholder="e.g. 01, 02" />
          <p class="description" style="margin-top:4px;font-size:11px">If left blank, it will automatically fall back to the item's listing index (01 to 06).</p></td></tr>
      <tr><th><label>Description</label></th>
          <td><textarea name="puja_desc" rows="5" style="width:100%"><?php echo esc_textarea($desc); ?></textarea></td></tr>
    </table>
    <p style="font-size:12px;color:#666;margin-top:12px">💡 <strong>Ordering tip:</strong> Use <em>Page Attributes → Order</em> (right sidebar) to control which card appears first. Lower number = displayed first.</p>
    <?php
}

add_action('save_post_sk_puja', 'sk_save_puja_meta');
function sk_save_puja_meta(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sk_puja_nonce']) || !wp_verify_nonce($_POST['sk_puja_nonce'], 'sk_puja_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, 'puja_desc', sanitize_textarea_field($_POST['puja_desc'] ?? ''));
    update_post_meta($post_id, 'puja_num',  sanitize_text_field($_POST['puja_num']  ?? ''));
}
