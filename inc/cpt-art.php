<?php
/**
 * Sacred Kompass — Custom Post Type: Art for Peace
 */
defined('ABSPATH') || exit;

add_action('init', 'sk_register_art_cpt', 10);
function sk_register_art_cpt(): void {
    register_post_type('sk_art', [
        'labels' => [
            'name'          => 'Art for Peace',
            'singular_name' => 'Artwork',
            'add_new_item'  => 'Add New Artwork',
            'edit_item'     => 'Edit Artwork',
            'menu_name'     => 'Art for Peace',
        ],
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => false, // nested via sk_nest_cpt_menus
        'supports'        => ['title', 'thumbnail', 'page-attributes'],
        'rewrite'         => ['slug' => 'art-for-peace', 'with_front' => false],
        'capability_type' => 'post',
        'has_archive'     => false,
        'show_in_rest'    => true,
        'menu_icon'       => 'dashicons-art',
    ]);
}

add_action('add_meta_boxes', 'sk_register_art_meta_boxes');
function sk_register_art_meta_boxes(): void {
    add_meta_box('sk_art_details', '★ Artwork Details', 'sk_art_meta_box_cb', 'sk_art', 'normal', 'high');
}

function sk_art_meta_box_cb(WP_Post $post): void {
    wp_nonce_field('sk_art_save', 'sk_art_nonce');
    $tag                = get_post_meta($post->ID, 'art_tag', true);
    $desc               = get_post_meta($post->ID, 'art_desc', true);
    $price              = get_post_meta($post->ID, 'art_price', true);
    $dimensions         = get_post_meta($post->ID, 'art_dimensions', true);
    $medium             = get_post_meta($post->ID, 'art_medium', true);
    $therapeutic_value  = get_post_meta($post->ID, 'art_therapeutic_value', true);
    $form_slug          = get_post_meta($post->ID, 'art_form_slug', true);
    $cta_url            = get_post_meta($post->ID, 'art_cta_url', true);
    $artist             = get_post_meta($post->ID, 'art_artist', true);
    $artist_type        = get_post_meta($post->ID, 'art_artist_type', true);
    if ($artist_type === '') {
        $artist_type = 'Person';
    }
    
    // Artwork image meta (stored as ID and URL fallback)
    $image_url = get_the_post_thumbnail_url($post->ID, 'medium');
    $image_id = 0;
    if (!$image_url) {
        $image_id  = (int) get_post_meta($post->ID, 'art_image_id', true);
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : get_post_meta($post->ID, 'art_image', true);
    }
    ?>
    <table class="form-table" style="width:100%">
      <tr>
        <th style="width:180px"><label>Category / Theme Tag</label></th>
        <td>
          <input type="text" name="art_tag" value="<?php echo esc_attr($tag); ?>" style="width:100%" placeholder="e.g. Reflection, Healing, Transformation, Peace" />
          <p class="description">Caps tag displayed on the homepage card and single layout.</p>
        </td>
      </tr>
      <tr>
        <th><label>Artist Name</label></th>
        <td>
          <input type="text" name="art_artist" value="<?php echo esc_attr($artist); ?>" style="width:100%" placeholder="e.g. Kalai Somoo (leave blank to default to site name)" />
          <p class="description">Name of the artwork creator. If blank, defaults to the site name (Sacred Kompass Collective).</p>
        </td>
      </tr>
      <tr>
        <th><label>Artist Type</label></th>
        <td>
          <select name="art_artist_type" style="width:100%">
            <option value="Person" <?php selected($artist_type, 'Person'); ?>>Person</option>
            <option value="Organization" <?php selected($artist_type, 'Organization'); ?>>Organization</option>
          </select>
          <p class="description">Select the schema type for the artist. Defaults to Person (or Organization if the name defaults to the site name).</p>
        </td>
      </tr>
      <tr>
        <th><label>Therapeutic Value / Meaning</label></th>
        <td>
          <textarea name="art_desc" rows="5" style="width:100%" placeholder="Describe the therapeutic story, emotional impact, and meaning of the artwork..."><?php echo esc_textarea($desc); ?></textarea>
          <p class="description">This detailed narrative is shown on the standalone artwork page.</p>
        </td>
      </tr>
      <tr>
        <th><label>Medium</label></th>
        <td>
          <input type="text" name="art_medium" value="<?php echo esc_attr($medium); ?>" style="width:100%" placeholder="e.g. Acrylic on linen, Oil on handmade paper" />
        </td>
      </tr>
      <tr>
        <th><label>Dimensions</label></th>
        <td>
          <input type="text" name="art_dimensions" value="<?php echo esc_attr($dimensions); ?>" style="width:100%" placeholder="e.g. 80 x 100 cm" />
        </td>
      </tr>
      <tr>
        <th><label>Price / Value</label></th>
        <td>
          <input type="text" name="art_price" value="<?php echo esc_attr($price); ?>" style="width:100%" placeholder="e.g. SGD 450 (or leave blank if private inquiry)" />
        </td>
      </tr>
      <tr>
        <th><label>Artwork Image</label></th>
        <td>
          <div id="sk-art-image-preview-wrap" style="<?php echo $image_url ? '' : 'display:none;'; ?>margin-bottom:8px">
            <img id="sk-art-image-preview" src="<?php echo esc_url($image_url ?: ''); ?>" alt="Artwork preview" style="max-width:200px;max-height:200px;height:auto;object-fit:contain;border:1px solid #ddd;border-radius:4px;" />
          </div>
          <input type="hidden" name="art_image_id" id="sk_art_image_id" value="<?php echo esc_attr($image_id ?: ''); ?>" />
          <input type="hidden" name="art_image"    id="sk_art_image_url" value="<?php echo esc_attr($image_url ?: ''); ?>" />
          <div style="display:flex;gap:6px">
            <button type="button" id="sk-art-image-btn" class="button button-small">Select Artwork Image</button>
            <button type="button" id="sk-art-image-remove" class="button button-small" style="color:#a00;<?php echo $image_url ? '' : 'display:none;'; ?>">Remove</button>
          </div>
          <script>
          jQuery(function($){
            var frame;
            var $preview = $('#sk-art-image-preview');
            var $wrap    = $('#sk-art-image-preview-wrap');
            var $idF     = $('#sk_art_image_id');
            var $urlF    = $('#sk_art_image_url');
            var $rmv     = $('#sk-art-image-remove');
            
            function setImg(id, url){
              $idF.val(id);
              $urlF.val(url);
              $preview.attr('src', url);
              $wrap.show();
              $rmv.show();
            }
            
            $('#sk-art-image-btn').on('click', function(e){
              e.preventDefault();
              if (frame) { frame.open(); return; }
              frame = wp.media({
                title: 'Select Artwork Image',
                button: { text: 'Use this image' },
                multiple: false,
                library: { type: 'image' }
              });
              frame.on('select', function(){
                var a = frame.state().get('selection').first().toJSON();
                var u = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
                setImg(a.id, u);
              });
              frame.open();
            });
            
            $rmv.on('click', function(e){
              e.preventDefault();
              $idF.val('');
              $urlF.val('');
              $preview.attr('src', '');
              $wrap.hide();
              $(this).hide();
            });
          });
          </script>
        </td>
      </tr>
      <tr>
        <th><label>Contact Form Slug / ID</label></th>
        <td>
          <input type="text" name="art_form_slug" value="<?php echo esc_attr($form_slug); ?>" style="width:100%" placeholder="e.g. contact" />
        </td>
      </tr>
      <tr>
        <th><label>Custom CTA URL</label></th>
        <td>
          <input type="url" name="art_cta_url" value="<?php echo esc_attr($cta_url); ?>" style="width:100%" placeholder="https://... (override redirect URL for inquiries)" />
        </td>
      </tr>
    </table>
    <?php
}

add_action('save_post_sk_art', 'sk_save_art_meta');
function sk_save_art_meta(int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sk_art_nonce']) || !wp_verify_nonce($_POST['sk_art_nonce'], 'sk_art_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $text_fields = ['art_tag', 'art_medium', 'art_dimensions', 'art_price', 'art_form_slug', 'art_artist', 'art_artist_type'];
    foreach ($text_fields as $f) {
        update_post_meta($post_id, $f, sanitize_text_field($_POST[$f] ?? ''));
    }
    
    update_post_meta($post_id, 'art_desc', sanitize_textarea_field($_POST['art_desc'] ?? ''));
    update_post_meta($post_id, 'art_cta_url', esc_url_raw($_POST['art_cta_url'] ?? ''));

    $img_id = (int) ($_POST['art_image_id'] ?? 0);
    if ($img_id > 0) {
        update_post_meta($post_id, 'art_image_id', $img_id);
        $url = wp_get_attachment_image_url($img_id, 'large') ?: '';
        update_post_meta($post_id, 'art_image', $url);
    } elseif (isset($_POST['art_image_id']) && $_POST['art_image_id'] === '') {
        update_post_meta($post_id, 'art_image_id', '');
        update_post_meta($post_id, 'art_image', '');
    } else {
        update_post_meta($post_id, 'art_image', esc_url_raw($_POST['art_image'] ?? ''));
    }

    // Force clear the transient cache so updates are immediately visible on the frontend
    delete_transient('sk_art_data');
}

// Columns listing customization
add_filter('manage_sk_art_posts_columns', function(array $cols): array {
    $new = [];
    foreach ($cols as $key => $label) {
        $new[$key] = $label;
        if ($key === 'title') {
            $new['art_image'] = 'Artwork';
            $new['art_tag']   = 'Theme / Tag';
            $new['art_medium'] = 'Medium';
            $new['art_price'] = 'Value';
        }
    }
    return $new;
});

add_action('manage_sk_art_posts_custom_column', function(string $col, int $id): void {
    if ($col === 'art_image') {
        $img_id = (int) get_post_meta($id, 'art_image_id', true);
        $url = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : get_post_meta($id, 'art_image', true);
        if ($url) {
            echo '<img src="' . esc_url($url) . '" style="width:50px;height:50px;object-fit:cover;border-radius:4px;border:1px solid #ddd">';
        } else {
            echo '<span style="color:#aaa;font-size:11px">No image</span>';
        }
    }
    if ($col === 'art_tag') {
        $tag = get_post_meta($id, 'art_tag', true);
        echo $tag ? esc_html($tag) : '<span style="color:#ccc">—</span>';
    }
    if ($col === 'art_medium') {
        $med = get_post_meta($id, 'art_medium', true);
        echo $med ? esc_html($med) : '<span style="color:#ccc">—</span>';
    }
    if ($col === 'art_price') {
        $val = get_post_meta($id, 'art_price', true);
        echo $val ? esc_html($val) : '<span style="color:#ccc">—</span>';
    }
}, 10, 2);
