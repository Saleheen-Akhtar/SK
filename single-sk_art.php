<?php
/**
 * Standalone Single Artwork Template — sk_art CPT.
 * URL: /art-for-peace/{slug}/
 */
get_header();

if ( ! have_posts() ) {
    get_footer();
    return;
}

the_post();
$id         = get_the_ID();
$tag        = get_post_meta($id, 'art_tag', true);
$desc       = get_post_meta($id, 'art_desc', true);
$medium     = get_post_meta($id, 'art_medium', true);
$dimensions = get_post_meta($id, 'art_dimensions', true);
$price      = get_post_meta($id, 'art_price', true);
$cta_url    = get_post_meta($id, 'art_cta_url', true);
$form_slug  = get_post_meta($id, 'art_form_slug', true);
$title      = get_the_title();

// Artwork Image
$image_url = get_the_post_thumbnail_url($id, 'full');
if ( ! $image_url ) {
    $image_id  = (int) get_post_meta($id, 'art_image_id', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : get_post_meta($id, 'art_image', true);
}

// Build Inquiry Link
$contact_base = sk_option('contact_url', home_url('/#contact'));
$inquiry_url = $contact_base;
if ($cta_url) {
    $inquiry_url = $cta_url;
} elseif ($form_slug) {
    $inquiry_url = home_url('/' . ltrim($form_slug, '/') . '/?artwork=' . urlencode($title));
} else {
    $separator = (strpos($contact_base, '?') !== false) ? '&' : '?';
    if (strpos($contact_base, '#') !== false) {
        $parts = explode('#', $contact_base, 2);
        $inquiry_url = $parts[0] . $separator . 'artwork=' . urlencode($title) . '#' . $parts[1];
    } else {
        $inquiry_url = $contact_base . $separator . 'artwork=' . urlencode($title);
    }
}
?>

<article class="sk-art-single" id="artwork-<?php echo $id; ?>">
  <div class="wrap sk-art-single-wrap">
    
    <!-- Left Column: Artwork Image -->
    <div class="sk-art-single-image-col">
      <a href="<?php echo esc_url( home_url( '/#art' ) ); ?>" class="sk-art-back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        <?php esc_html_e( 'Back to Gallery', 'sacred-kompass' ); ?>
      </a>
      
      <?php if ( $image_url ) : ?>
        <div class="sk-art-single-frame">
          <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="eager" />
        </div>
      <?php endif; ?>
    </div>

    <!-- Right Column: Editorial & Context Details -->
    <div class="sk-art-single-details-col">
      <header class="sk-art-single-header">
        <?php if ( $tag ) : ?>
          <span class="sk-art-single-tag"><?php echo esc_html( $tag ); ?></span>
        <?php endif; ?>
        <h1 class="sk-art-single-title"><?php echo esc_html( $title ); ?></h1>
      </header>

      <?php if ( $desc ) : ?>
        <div class="sk-art-single-story">
          <h2 class="sk-art-story-label"><?php esc_html_e( 'The Creation Story & Therapeutic Value', 'sacred-kompass' ); ?></h2>
          <div class="sk-art-story-content">
            <?php echo wpautop( esc_html( $desc ) ); ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Specifications Table -->
      <?php if ( $medium || $dimensions || $price ) : ?>
        <div class="sk-art-specifications">
          <table class="sk-art-spec-table">
            <?php if ( $medium ) : ?>
              <tr>
                <th><?php esc_html_e( 'Medium', 'sacred-kompass' ); ?></th>
                <td><?php echo esc_html( $medium ); ?></td>
              </tr>
            <?php endif; ?>
            <?php if ( $dimensions ) : ?>
              <tr>
                <th><?php esc_html_e( 'Dimensions', 'sacred-kompass' ); ?></th>
                <td><?php echo esc_html( $dimensions ); ?></td>
              </tr>
            <?php endif; ?>
            <?php if ( $price ) : ?>
              <tr>
                <th><?php esc_html_e( 'Exchange Value', 'sacred-kompass' ); ?></th>
                <td><?php echo esc_html( $price ); ?></td>
              </tr>
            <?php endif; ?>
          </table>
        </div>
      <?php endif; ?>

      <!-- Inquiry Action Button -->
      <div class="sk-art-single-cta">
        <a href="<?php echo esc_url( $inquiry_url ); ?>" class="btn btn-primary sk-art-inquire-btn">
          <?php esc_html_e( 'Inquire About This Piece', 'sacred-kompass' ); ?>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>

  </div>
</article>

<?php get_footer(); ?>
