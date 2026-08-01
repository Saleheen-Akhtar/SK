<?php
/**
 * Standalone Single Artwork Template (Podium Style)
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

<article class="sk-art-single" id="artwork-<?php echo $id; ?>" style="background-color:var(--color-surface-base); padding:var(--space-3) 0; min-height:100vh;">
  <div class="wrap sk-art-single-wrap" style="display:grid; grid-template-columns:1fr; gap:var(--space-2);">
    
    <!-- Left Column: Artwork Image -->
    <div class="sk-art-single-image-col">
      <a href="<?php echo esc_url( home_url( '/#art' ) ); ?>" class="body-small" style="display:inline-flex; align-items:center; gap:0.5rem; margin-bottom:var(--space-1); text-decoration:underline;">
        &larr; <?php esc_html_e( 'Back to Gallery', 'sacred-kompass' ); ?>
      </a>
      
      <?php if ( $image_url ) : ?>
        <div class="sk-art-single-frame">
          <div style="aspect-ratio:4/5; overflow:hidden;">
             <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="eager" style="width:100%; height:100%; object-fit:cover;" />
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Right Column: Details -->
    <div class="sk-art-single-details-col body-ui">
      <header class="sk-art-single-header" style="margin-bottom:var(--space-2);">
        <?php if ( $tag ) : ?>
          <span class="eyebrow"><?php echo esc_html( $tag ); ?></span>
        <?php endif; ?>
        <h1 class="display-h2"><?php echo esc_html( $title ); ?></h1>
      </header>

      <?php if ( $desc ) : ?>
        <div class="sk-art-single-story" style="margin-bottom:var(--space-2);">
          <h2 class="display-h3" style="margin-bottom:var(--space-1);"><?php esc_html_e( 'The Creation Story', 'sacred-kompass' ); ?></h2>
          <div class="sk-art-story-content body-serif">
            <?php echo wpautop( esc_html( $desc ) ); ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Specifications Table -->
      <?php if ( $medium || $dimensions || $price ) : ?>
        <div class="sk-art-specifications" style="margin-bottom:var(--space-2);">
          <table class="sk-art-spec-table body-ui" style="width:100%; text-align:left; border-collapse:collapse;">
            <?php if ( $medium ) : ?>
              <tr style="border-bottom:1px solid var(--color-surface-strong);">
                <th style="padding:0.5rem 0; font-weight:var(--font-weight-base); color:var(--color-text-tertiary);"><?php esc_html_e( 'Medium', 'sacred-kompass' ); ?></th>
                <td style="padding:0.5rem 0; text-align:right;"><?php echo esc_html( $medium ); ?></td>
              </tr>
            <?php endif; ?>
            <?php if ( $dimensions ) : ?>
              <tr style="border-bottom:1px solid var(--color-surface-strong);">
                <th style="padding:0.5rem 0; font-weight:var(--font-weight-base); color:var(--color-text-tertiary);"><?php esc_html_e( 'Dimensions', 'sacred-kompass' ); ?></th>
                <td style="padding:0.5rem 0; text-align:right;"><?php echo esc_html( $dimensions ); ?></td>
              </tr>
            <?php endif; ?>
            <?php if ( $price ) : ?>
              <tr style="border-bottom:1px solid var(--color-surface-strong);">
                <th style="padding:0.5rem 0; font-weight:var(--font-weight-base); color:var(--color-text-tertiary);"><?php esc_html_e( 'Value', 'sacred-kompass' ); ?></th>
                <td style="padding:0.5rem 0; text-align:right;"><?php echo esc_html( $price ); ?></td>
              </tr>
            <?php endif; ?>
          </table>
        </div>
      <?php endif; ?>

      <!-- Inquiry Action Button -->
      <div class="sk-art-single-cta">
        <a href="<?php echo esc_url( $inquiry_url ); ?>" class="btn-outline">
          <?php esc_html_e( 'Inquire About This Piece', 'sacred-kompass' ); ?>
        </a>
      </div>
    </div>

  </div>
</article>

<?php get_footer(); ?>
