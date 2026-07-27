<?php
/**
 * Sacred Kompass — Custom Post Type: Stories
 */
defined('ABSPATH') || exit;

add_action( 'init', 'sk_register_story_cpt', 10 );
function sk_register_story_cpt(): void {
    register_post_type( 'sk_story', [
        'labels' => [
            'name'          => 'Stories',
            'singular_name' => 'Story',
            'add_new_item'  => 'Add New Story',
            'edit_item'     => 'Edit Story',
            'menu_name'     => 'Stories',
        ],
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => false,
        'show_in_rest'    => true,        // Enables Gutenberg
        'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
        'rewrite'         => [ 'slug' => 'stories', 'with_front' => false ],
        'capability_type' => 'post',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-book-alt',
    ] );
}

add_action( 'admin_menu', 'sk_nest_story_menu', 100 );
function sk_nest_story_menu(): void {
    add_submenu_page(
        'sk-settings',
        'Stories',
        '✦ Stories',
        'edit_posts',
        'edit.php?post_type=sk_story'
    );
}

add_action( 'add_meta_boxes', 'sk_register_story_meta_boxes' );
function sk_register_story_meta_boxes(): void {
    add_meta_box(
        'sk_story_details',
        '✦ Story Details',
        'sk_story_meta_box_cb',
        'sk_story',
        'side',
        'high'
    );
}

function sk_story_meta_box_cb( WP_Post $post ): void {
    wp_nonce_field( 'sk_story_save', 'sk_story_nonce' );
    $pull_quote   = get_post_meta( $post->ID, 'story_pull_quote',      true );
    $category     = get_post_meta( $post->ID, 'story_category',        true );
    $author_name  = get_post_meta( $post->ID, 'story_author_name',     true );
    $author_title = get_post_meta( $post->ID, 'story_author_title',    true );
    $author_location = get_post_meta( $post->ID, 'story_author_location', true );
    $cover_url    = get_post_meta( $post->ID, 'story_cover_image_url', true );
    $cover_id     = (int) get_post_meta( $post->ID, 'story_cover_image_id', true );
    $read_time    = get_post_meta( $post->ID, 'story_read_time',       true );
    $featured     = (bool) get_post_meta( $post->ID, 'story_featured', true );
    $cover_display = $cover_id ? wp_get_attachment_image_url( $cover_id, 'thumbnail' ) : $cover_url;
    ?>
    <p style="font-size:11px;color:#666;margin:0 0 12px">The <strong>title</strong> and <strong>body</strong> are edited in the main Gutenberg area. These fields add structured metadata shown on cards and the stories page.</p>
    <table style="width:100%;border-collapse:collapse">
      <tr style="margin-bottom:10px;display:block">
        <td style="display:block;padding:0 0 4px">
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Pull Quote <small style="font-weight:400;color:#888">(shown on cards)</small></label>
          <textarea name="story_pull_quote" rows="3" style="width:100%;font-size:12px;resize:vertical" placeholder="One evocative sentence from the story — shown as excerpt on cards."><?php echo esc_textarea( $pull_quote ); ?></textarea>
        </td>
      </tr>
      <tr style="display:block;margin-bottom:10px">
        <td style="display:block">
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Category / Tag</label>
          <input type="text" name="story_category" value="<?php echo esc_attr( $category ); ?>" style="width:100%;font-size:12px" placeholder="e.g. Healing · Grief · Leadership" />
          <p style="font-size:10px;color:#888;margin:3px 0 0">Badge shown on card. Keep brief.</p>
        </td>
      </tr>
      <tr style="display:block;margin-bottom:10px">
        <td style="display:block">
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Story By (name)</label>
          <input type="text" name="story_author_name" value="<?php echo esc_attr( $author_name ); ?>" style="width:100%;font-size:12px" placeholder="e.g. Saleheen Akhtar" />
        </td>
      </tr>
      <tr style="display:block;margin-bottom:10px">
        <td style="display:block">
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Author Title / Role <small style="font-weight:400;color:#888">(optional)</small></label>
          <input type="text" name="story_author_title" value="<?php echo esc_attr( $author_title ); ?>" style="width:100%;font-size:12px" placeholder="e.g. Founder · Jyotishi" />
        </td>
      </tr>
      <tr style="display:block;margin-bottom:10px">
        <td style="display:block">
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Author Location <small style="font-weight:400;color:#888">(optional)</small></label>
          <input type="text" name="story_author_location" value="<?php echo esc_attr( $author_location ); ?>" style="width:100%;font-size:12px" placeholder="e.g. Singapore · London" />
        </td>
      </tr>
      <tr style="display:block;margin-bottom:10px">
        <td style="display:block">
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Reading Time <small style="font-weight:400;color:#888">(optional)</small></label>
          <input type="text" name="story_read_time" value="<?php echo esc_attr( $read_time ); ?>" style="width:100%;font-size:12px" placeholder="e.g. 5 min read" />
        </td>
      </tr>
      <tr style="display:block;margin-bottom:10px">
        <td style="display:block">
          <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Cover Image</label>
          <div id="sk-story-cover-preview-wrap" style="<?php echo $cover_display ? '' : 'display:none;'; ?>margin-bottom:8px">
            <img id="sk-story-cover-preview" src="<?php echo esc_url( $cover_display ?: '' ); ?>" alt="<?php esc_attr_e('Story cover image preview', 'sacred-kompass'); ?>" style="width:100%;height:80px;object-fit:cover;border-radius:4px;border:1px solid #ddd" />
          </div>
          <input type="hidden" name="story_cover_image_id"  id="sk_story_cover_id"  value="<?php echo esc_attr( $cover_id ); ?>" />
          <input type="hidden" name="story_cover_image_url" id="sk_story_cover_url" value="<?php echo esc_attr( $cover_display ?: $cover_url ); ?>" />
          <div style="display:flex;gap:6px">
            <button type="button" id="sk-story-cover-btn"    class="button button-small">Select Image</button>
            <button type="button" id="sk-story-cover-remove" class="button button-small" style="color:#a00;<?php echo $cover_display ? '' : 'display:none;'; ?>">Remove</button>
          </div>
          <script>
          jQuery(function($){
            var frame;
            var $preview = $('#sk-story-cover-preview');
            var $wrap    = $('#sk-story-cover-preview-wrap');
            var $idF     = $('#sk_story_cover_id');
            var $urlF    = $('#sk_story_cover_url');
            var $rmv     = $('#sk-story-cover-remove');
            function setImg(id,url){ $idF.val(id); $urlF.val(url); $preview.attr('src',url); $wrap.show(); $rmv.show(); }
            $('#sk-story-cover-btn').on('click',function(e){
              e.preventDefault();
              if(frame){frame.open();return;}
              frame=wp.media({title:'Select Cover',button:{text:'Use this image'},multiple:false,library:{type:'image'}});
              frame.on('select',function(){ var a=frame.state().get('selection').first().toJSON(); var u=(a.sizes&&a.sizes.medium)?a.sizes.medium.url:a.url; setImg(a.id,u); });
              frame.open();
            });
            $rmv.on('click',function(e){ e.preventDefault(); $idF.val(''); $urlF.val(''); $preview.attr('src',''); $wrap.hide(); $(this).hide(); });
          });
          </script>
        </td>
      </tr>
      <tr style="display:block;margin-bottom:6px">
        <td style="display:block">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;font-weight:600">
            <input type="checkbox" name="story_featured" value="1"<?php checked( $featured ); ?>>
            Feature on homepage (shows in teaser grid)
          </label>
        </td>
      </tr>
    </table>
    <?php
}

add_action( 'admin_notices', function(): void {
    $screen = get_current_screen();
    if ( ! $screen || $screen->id !== 'edit-sk_story' ) return;
    echo '<div class="notice notice-info" style="border-left-color:#c4a02a;padding:12px 16px">
        <p style="margin:0 0 8px"><strong>✦ Stories of the Soul</strong> — Each card on the website pulls from these fields per story:</p>
        <ul style="list-style:disc;margin:0 0 8px 1.5rem">
            <li><strong>Title</strong> — the card headline (set in the main Title field above)</li>
            <li><strong>Cover Image</strong> — the card photo (Story Details sidebar → Select Image)</li>
            <li><strong>Category / Tag</strong> — orange label e.g. "Healing &amp; Self-Love" (Story Details sidebar)</li>
            <li><strong>Pull Quote</strong> — 1–2 sentence excerpt shown on the card (Story Details sidebar)</li>
            <li><strong>Feature on homepage</strong> — tick the checkbox to include this story in the homepage preview grid</li>
        </ul>
        <p style="margin:0">To set the <strong>hero image</strong> on the /stories/ page header: 
        <a href="admin.php?page=sk-settings">★ Sacred Kompass → Settings → Stories Preview &amp; Stories Page → Stories Page Hero Image</a>.</p>
    </div>';
} );

add_action( 'save_post_sk_story', 'sk_save_story_meta' );
function sk_save_story_meta( int $post_id ): void {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['sk_story_nonce'] ) || ! wp_verify_nonce( $_POST['sk_story_nonce'], 'sk_story_save' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    update_post_meta( $post_id, 'story_pull_quote',      sanitize_textarea_field( $_POST['story_pull_quote']      ?? '' ) );
    update_post_meta( $post_id, 'story_category',        sanitize_text_field(     $_POST['story_category']        ?? '' ) );
    update_post_meta( $post_id, 'story_author_name',     sanitize_text_field(     $_POST['story_author_name']     ?? '' ) );
    update_post_meta( $post_id, 'story_author_title',    sanitize_text_field(     $_POST['story_author_title']    ?? '' ) );
    update_post_meta( $post_id, 'story_author_location', sanitize_text_field(     $_POST['story_author_location'] ?? '' ) );
    update_post_meta( $post_id, 'story_read_time',       sanitize_text_field(     $_POST['story_read_time']       ?? '' ) );
    update_post_meta( $post_id, 'story_featured',        isset( $_POST['story_featured'] ) ? '1' : '' );

    $cover_id = (int) ( $_POST['story_cover_image_id'] ?? 0 );
    if ( $cover_id > 0 ) {
        update_post_meta( $post_id, 'story_cover_image_id',  $cover_id );
        $url = wp_get_attachment_image_url( $cover_id, 'medium' ) ?: '';
        update_post_meta( $post_id, 'story_cover_image_url', $url );
    } elseif ( isset( $_POST['story_cover_image_id'] ) && $_POST['story_cover_image_id'] === '' ) {
        update_post_meta( $post_id, 'story_cover_image_id',  '' );
        update_post_meta( $post_id, 'story_cover_image_url', '' );
    } else {
        update_post_meta( $post_id, 'story_cover_image_url', esc_url_raw( $_POST['story_cover_image_url'] ?? '' ) );
    }

    // Force clear the transient cache so updates are immediately visible on the frontend
    delete_transient( 'sk_stories_preview_data_v4' );
}

add_filter( 'manage_sk_story_posts_columns', function( array $cols ): array {
    $new = [];
    foreach ( $cols as $key => $label ) {
        $new[ $key ] = $label;
        if ( $key === 'title' ) {
            $new['story_cover']    = 'Cover';
            $new['story_category'] = 'Category';
            $new['story_featured'] = '★ Featured';
        }
    }
    unset( $new['date'] );
    $new['date'] = $cols['date'] ?? 'Date';
    return $new;
} );

add_action( 'manage_sk_story_posts_custom_column', function( string $col, int $id ): void {
    if ( $col === 'story_cover' ) {
        $cid = (int) get_post_meta( $id, 'story_cover_image_id', true );
        $url = $cid ? wp_get_attachment_image_url( $cid, 'thumbnail' )
                    : get_post_meta( $id, 'story_cover_image_url', true );
        if ( ! $url && has_post_thumbnail( $id ) ) $url = get_the_post_thumbnail_url( $id, 'thumbnail' );
        if ( $url ) echo '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( get_the_title( $id ) ) . '" style="width:60px;height:40px;object-fit:cover;border-radius:4px;border:1px solid #ddd">';
        else echo '<span style="color:#999;font-size:11px">No image</span>';
    }
    if ( $col === 'story_category' ) {
        $cat = get_post_meta( $id, 'story_category', true );
        echo $cat ? '<span style="background:#fdf3e7;color:#a06820;border:1px solid #f0d9a8;border-radius:3px;padding:2px 8px;font-size:11px">' . esc_html( $cat ) . '</span>'
                  : '<span style="color:#ccc">—</span>';
    }
    if ( $col === 'story_featured' ) {
        $featured = get_post_meta( $id, 'story_featured', true );
        echo $featured ? '<span style="color:#c4a02a;font-size:16px" title="Featured on homepage">★</span>'
                       : '<span style="color:#ddd;font-size:16px">☆</span>';
    }
}, 10, 2 );
