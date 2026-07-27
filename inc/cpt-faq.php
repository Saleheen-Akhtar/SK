<?php
/**
 * Sacred Kompass — Custom Post Type: FAQ & Dedicated FAQ Category Taxonomy
 */
defined('ABSPATH') || exit;

add_action('init', 'sk_register_faq_cpt', 10);
function sk_register_faq_cpt(): void {
    register_post_type('sk_faq', [
        'labels' => [
            'name'               => 'FAQ',
            'singular_name'      => 'FAQ Item',
            'add_new_item'       => 'Add New FAQ Item',
            'edit_item'          => 'Edit FAQ Item',
            'menu_name'          => 'FAQ'
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_position'   => 22,
        'menu_icon'       => 'dashicons-editor-help',
        'supports'        => ['title', 'page-attributes'],
        'taxonomies'      => ['sk_faq_category'],
        'rewrite'         => false,
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => true,
    ]);

    // Register Dedicated Taxonomy for FAQ Categories
    register_taxonomy('sk_faq_category', 'sk_faq', [
        'labels' => [
            'name'              => 'FAQ Categories',
            'singular_name'     => 'FAQ Category',
            'search_items'      => 'Search FAQ Categories',
            'all_items'         => 'All FAQ Categories',
            'edit_item'         => 'Edit FAQ Category',
            'update_item'       => 'Update FAQ Category',
            'add_new_item'      => 'Add New FAQ Category',
            'new_item_name'     => 'New FAQ Category Name',
            'menu_name'         => 'FAQ Categories',
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'faq-category'],
        'show_in_rest'      => true,
    ]);
}

add_action('add_meta_boxes', 'sk_register_faq_meta_boxes');
function sk_register_faq_meta_boxes(): void {
    add_meta_box('sk_faq_answer', 'FAQ Answer Details', 'sk_faq_meta_box', 'sk_faq', 'normal', 'high');
}

function sk_faq_meta_box(WP_Post $post): void {
    wp_nonce_field('sk_faq_save', 'sk_faq_nonce');
    $answer = get_post_meta($post->ID, 'faq_answer', true);
    $group  = get_post_meta($post->ID, 'faq_group',  true);
    ?>
    <table class="form-table" style="width:100%">
      <tr><th style="width:180px"><label>Group / Category Name</label></th>
          <td><input type="text" name="faq_group" value="<?php echo esc_attr($group); ?>" style="width:320px" placeholder="e.g. Vedic Astrology, Consultations" />
          <p class="description" style="margin-top:4px;font-size:11px">Select a Category from the right sidebar <strong>FAQ Categories</strong> taxonomy or type a category name here. Speech bubbles are generated 100% dynamically on the homepage.</p></td></tr>
      <tr><th><label>Answer Text</label></th>
          <td><textarea name="faq_answer" rows="6" style="width:100%"><?php echo esc_textarea($answer); ?></textarea></td></tr>
    </table>
    <p style="font-size:12px;color:#666;margin-top:12px">💡 <strong>Ordering tip:</strong> Use <em>Page Attributes → Order</em> (right sidebar) to control the display sequence of FAQ items.</p>
    <?php
}

add_action('save_post_sk_faq', 'sk_save_faq_meta');
function sk_save_faq_meta(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sk_faq_nonce']) || !wp_verify_nonce($_POST['sk_faq_nonce'], 'sk_faq_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, 'faq_answer', sanitize_textarea_field($_POST['faq_answer'] ?? ''));
    update_post_meta($post_id, 'faq_group',  sanitize_text_field($_POST['faq_group']  ?? ''));
}
