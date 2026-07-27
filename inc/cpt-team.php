<?php
/**
 * Sacred Kompass — Custom Post Type: Team Members
 */
defined('ABSPATH') || exit;

add_action('init', 'sk_register_team_cpt', 10);
function sk_register_team_cpt(): void {
    register_post_type('sk_team', [
        'labels' => [
            'name'          => 'Team Members',
            'singular_name' => 'Team Member',
            'add_new_item'  => 'Add New Team Member',
            'edit_item'     => 'Edit Team Member',
            'menu_name'     => 'Team Members',
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => false,            // registered via sk_nest_cpt_menus — prevents duplicate entry
        'supports'        => ['title', 'thumbnail', 'page-attributes'],
        'rewrite'         => false,
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => true,
        'menu_icon'       => 'dashicons-groups',
    ]);
}

add_action('add_meta_boxes', 'sk_register_team_meta_boxes');
function sk_register_team_meta_boxes(): void {
    add_meta_box('sk_team_details', '★ Team Member Details', 'sk_team_meta_box_cb', 'sk_team', 'normal', 'high');
    add_meta_box('sk_team_image_url', '★ Portrait Photo (URL or Upload)', 'sk_team_image_meta_box_cb', 'sk_team', 'side', 'high');
    add_meta_box('sk_team_founder_flag', '⚑ Founder Card', 'sk_team_founder_flag_cb', 'sk_team', 'side', 'high');
}

function sk_team_image_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_team_image_save', 'sk_team_image_nonce');
    $attachment_id = (int) get_post_meta($post->ID, 'team_image_id', true);
    $img_url       = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : get_post_meta($post->ID, 'team_image', true);

    echo '<p style="font-size:12px;color:#666;margin-top:0">Upload or select a portrait photo from the Media Library. Drag &amp; drop onto the button below, or click to browse.</p>';

    echo '<input type="hidden" name="team_image_id" id="sk_team_image_id" value="' . esc_attr($attachment_id) . '" />';
    echo '<input type="hidden" name="team_image"    id="sk_team_image_url" value="' . esc_attr($img_url ?: '') . '" />';

    $preview_style = $img_url ? '' : 'display:none;';
    echo '<div id="sk-team-preview-wrap" style="margin-bottom:10px;' . $preview_style . '">';
    echo '<img id="sk-team-preview" src="' . esc_url($img_url ?: '') . '" alt="' . esc_attr__('Team member photo preview', 'sacred-kompass') . '" style="width:100%;border-radius:6px;object-fit:cover;aspect-ratio:3/4;max-height:240px" />';
    echo '</div>';

    echo '<div style="display:flex;gap:8px;flex-wrap:wrap">';
    echo '<button type="button" id="sk-team-upload-btn" class="button">'
       . '<span class="dashicons dashicons-upload" style="vertical-align:middle;margin-right:4px"></span>Upload / Select Photo</button>';
    echo '<button type="button" id="sk-team-remove-btn" class="button" style="color:#a00;' . ($img_url ? '' : 'display:none;') . '">Remove Photo</button>';
    echo '</div>';

    echo '<p style="font-size:11px;color:#888;margin-top:8px">Min 520×700px portrait recommended. '
       . 'Drag a file directly onto the <em>Upload / Select Photo</em> button to upload without opening the picker. '
       . 'Order members with the <strong>Order</strong> field (Page Attributes box).</p>';
    ?>
    <script>
    jQuery(function($){
        var frame;
        var $preview  = $('#sk-team-preview');
        var $wrap     = $('#sk-team-preview-wrap');
        var $idField  = $('#sk_team_image_id');
        var $urlField = $('#sk_team_image_url');
        var $removeBtn= $('#sk-team-remove-btn');

        function setImage(id, url) {
            $idField.val(id);
            $urlField.val(url);
            $preview.attr('src', url);
            $wrap.show();
            $removeBtn.show();
        }

        $('#sk-team-upload-btn').on('click', function(e){
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title:    'Select Portrait Photo',
                button:   { text: 'Use this photo' },
                multiple: false,
                library:  { type: 'image' },
            });
            frame.on('select', function(){
                var att = frame.state().get('selection').first().toJSON();
                var url = (att.sizes && att.sizes.medium) ? att.sizes.medium.url : att.url;
                setImage(att.id, url);
            });
            frame.open();
        });

        $('#sk-team-upload-btn').on('dragover', function(e){
            e.preventDefault();
            $(this).css('background', '#f0f6ff');
        }).on('dragleave', function(){
            $(this).css('background', '');
        }).on('drop', function(e){
            e.preventDefault();
            $(this).css('background', '');
            var file = e.originalEvent.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            var formData = new FormData();
            formData.append('action',   'upload-attachment');
            formData.append('async-upload', file);
            formData.append('name',     file.name);
            formData.append('_wpnonce', '<?php echo wp_create_nonce('media-form'); ?>');
            formData.append('post_id',  '<?php echo (int) $post->ID; ?>');

            $.ajax({
                url:         ajaxurl,
                type:        'POST',
                data:        formData,
                processData: false,
                contentType: false,
                success: function(res){
                    if (res && res.success && res.data && res.data.id) {
                        var url = (res.data.sizes && res.data.sizes.medium) ? res.data.sizes.medium.url : res.data.url;
                        setImage(res.data.id, url);
                    }
                }
            });
        });

        $removeBtn.on('click', function(e){
            e.preventDefault();
            $idField.val('');
            $urlField.val('');
            $preview.attr('src', '');
            $wrap.hide();
            $(this).hide();
        });
    });
    </script>
    <?php
}

function sk_team_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_team_save', 'sk_team_nonce');
    $first     = get_post_meta($post->ID, 'team_first_name', true);
    $last      = get_post_meta($post->ID, 'team_last_name',  true);
    $origin    = get_post_meta($post->ID, 'team_origin',     true);
    $role      = get_post_meta($post->ID, 'team_role',       true);
    $bio       = get_post_meta($post->ID, 'team_bio',        true);
    $tags      = get_post_meta($post->ID, 'team_tags',       true);
    $linkedin  = get_post_meta($post->ID, 'team_linkedin',   true);
    $instagram = get_post_meta($post->ID, 'team_instagram',  true);
    ?>
    <table class="form-table" style="width:100%">
      <tr><th style="width:160px"><label>First Name</label></th>
          <td><input type="text" name="team_first_name" value="<?php echo esc_attr($first); ?>" style="width:100%" /></td></tr>
      <tr><th><label>Last Name</label></th>
          <td><input type="text" name="team_last_name" value="<?php echo esc_attr($last); ?>" style="width:100%" /></td></tr>
      <tr><th><label>Origin / Country</label></th>
          <td><input type="text" name="team_origin" value="<?php echo esc_attr($origin); ?>" placeholder="e.g. Singapore" style="width:100%" /></td></tr>
      <tr><th><label>Role / Title</label></th>
          <td><input type="text" name="team_role" value="<?php echo esc_attr($role); ?>" placeholder="e.g. Founder and Lead Guide" style="width:100%" /></td></tr>
      <tr><th><label>Bio</label></th>
          <td><textarea name="team_bio" rows="5" style="width:100%"><?php echo esc_textarea($bio); ?></textarea></td></tr>
      <tr><th><label>Expertise Tags<br><small style="font-weight:300">(one per line)</small></label></th>
          <td><textarea name="team_tags" rows="4" style="width:100%" placeholder="Vedic Philosophy&#10;Jyotish Astrology&#10;Coaching"><?php echo esc_textarea($tags); ?></textarea></td></tr>
      <tr><td colspan="2"><hr style="margin:8px 0;border:none;border-top:1px solid #f0f0f1"></td></tr>
      <tr><th><label>LinkedIn URL</label></th>
          <td><input type="url" name="team_linkedin" value="<?php echo esc_attr($linkedin); ?>" style="width:100%" placeholder="https://linkedin.com/in/username (optional)" /></td></tr>
      <tr><th><label>Instagram URL</label></th>
          <td><input type="url" name="team_instagram" value="<?php echo esc_attr($instagram); ?>" style="width:100%" placeholder="https://instagram.com/username (optional)" /></td></tr>
    </table>
    <p style="font-size:12px;color:#666;margin-top:12px">💡 <strong>Tip:</strong> Use <em>Page Attributes → Order</em> (right sidebar) to control display order. Lower number = displayed first (big card).</p>
    <?php
}

function sk_team_founder_flag_cb(WP_Post $post): void {
    wp_nonce_field('sk_team_founder_flag_save', 'sk_team_founder_flag_nonce');
    $is_founder = (bool) get_post_meta($post->ID, 'team_is_founder', true);
    echo '<p style="font-size:12px;color:#666;margin:0 0 10px">Mark this person as a Founder. Founders always appear in the two dedicated founder cards (right column of the Founders section), regardless of menu order.</p>';
    echo '<label style="display:flex;align-items:center;gap:8px;font-weight:600;cursor:pointer">';
    echo '<input type="checkbox" name="team_is_founder" value="1"' . checked($is_founder, true, false) . ' style="width:16px;height:16px" />';
    echo 'This is a Founder</label>';
    echo '<p style="font-size:11px;color:#888;margin-top:8px">Only two founder cards are shown. If more than two members are marked as founders, the first two (by Order) will appear.</p>';
}

add_action('save_post_sk_team', 'sk_save_team_founder_flag', 10, 1);
add_action('save_post_sk_team', 'sk_save_team_meta',         20, 1);

function sk_save_team_founder_flag(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sk_team_founder_flag_nonce']) || !wp_verify_nonce($_POST['sk_team_founder_flag_nonce'], 'sk_team_founder_flag_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    update_post_meta($post_id, 'team_is_founder', isset($_POST['team_is_founder']) ? '1' : '');
}

function sk_save_team_meta(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sk_team_nonce']) || !wp_verify_nonce($_POST['sk_team_nonce'], 'sk_team_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['team_first_name','team_last_name','team_origin','team_role'];
    foreach ($fields as $f) {
        update_post_meta($post_id, $f, sanitize_text_field($_POST[$f] ?? ''));
    }
    update_post_meta($post_id, 'team_bio',  sanitize_textarea_field($_POST['team_bio']  ?? ''));
    update_post_meta($post_id, 'team_tags', sanitize_textarea_field($_POST['team_tags'] ?? ''));

    $img_id = (int) ($_POST['team_image_id'] ?? 0);
    if ($img_id > 0) {
        update_post_meta($post_id, 'team_image_id', $img_id);
        $url = wp_get_attachment_image_url($img_id, 'large') ?: '';
        if ($url) update_post_meta($post_id, 'team_image', $url);
    } elseif (isset($_POST['team_image_id']) && $_POST['team_image_id'] === '') {
        update_post_meta($post_id, 'team_image_id', '');
        update_post_meta($post_id, 'team_image', '');
    } elseif (!empty($_POST['team_image']) && str_starts_with($_POST['team_image'], 'http')) {
        update_post_meta($post_id, 'team_image', esc_url_raw($_POST['team_image']));
    }
    update_post_meta($post_id, 'team_linkedin',  esc_url_raw($_POST['team_linkedin']  ?? ''));
    update_post_meta($post_id, 'team_instagram', esc_url_raw($_POST['team_instagram'] ?? ''));
}

/* Helper so founders.php can get thumbnail URL */
if (!function_exists('get_post_thumbnail_url')) {
    function get_post_thumbnail_url(int $post_id, string $size = 'large'): string {
        $id = get_post_thumbnail_id($post_id);
        return $id ? (wp_get_attachment_image_url($id, $size) ?: '') : '';
    }
}
