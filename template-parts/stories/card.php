<?php
/**
 * Shared Template Partial: Stories Grid Card
 */
defined('ABSPATH') || exit;

$s = $args['story'] ?? [];
if (empty($s)) return;

$read_more_label = sk_option('sk_stories_read_more', 'Read Her Story');
?>
<article
  class="sk-spg-card"
  data-category="<?php echo esc_attr( sanitize_title( $s['category'] ) ); ?>"
  data-timestamp="<?php echo esc_attr( get_the_time( 'U', $s['id'] ) ); ?>"
  <?php if (!empty($s['date'])) : ?>data-date="<?php echo esc_attr( $s['date'] ); ?>"<?php endif; ?>
  data-story-item
>
  <!-- Cover image with quote badge -->
  <div class="sk-spg-card-img">
    <?php if ( !empty($s['cover']) ) : ?>
    <img src="<?php echo esc_url( $s['cover'] ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s cover', 'sacred-kompass' ), $s['title'] ) ); ?>" loading="lazy" />
    <?php else : ?>
    <div class="sk-spg-card-img-placeholder" aria-hidden="true">
      <span><?php echo esc_html( mb_substr( $s['title'], 0, 1 ) ); ?></span>
    </div>
    <?php endif; ?>
    <div class="sk-spg-quote-badge" aria-hidden="true">
      <svg width="13" height="10" viewBox="0 0 14 11" fill="none">
        <path d="M0 11V6.6C0 4.84 0.547 3.327 1.64 2.06C2.733 0.793 4.187 0.027 6 0V1.94C4.987 2.107 4.16 2.573 3.52 3.34C2.88 4.107 2.56 5 2.56 6.02H5.6V11H0ZM8.4 11V6.6C8.4 4.84 8.947 3.327 10.04 2.06C11.133 0.793 12.587 0.027 14.4 0V1.94C13.387 2.107 12.56 2.573 11.92 3.34C11.28 4.107 10.96 5 10.96 6.02H14V11H8.4Z" fill="currentColor"/>
      </svg>
    </div>
  </div>

  <!-- Card body -->
  <div class="sk-spg-card-body">
    <?php if ( !empty($s['category']) || ! empty( $s['author_location'] ) ) : ?>
    <p class="sk-spg-card-cat">
      <?php
      $meta_parts = [];
      if ( !empty($s['category']) ) $meta_parts[] = esc_html( $s['category'] );
      if ( ! empty( $s['author_location'] ) ) $meta_parts[] = esc_html( $s['author_location'] );
      echo implode( ' · ', $meta_parts );
      ?>
    </p>
    <?php endif; ?>
    <h3 class="sk-spg-card-title"><?php echo esc_html( $s['title'] ); ?></h3>
    <?php $card_text = !empty($s['pull_quote']) ? $s['pull_quote'] : (!empty($s['excerpt']) ? $s['excerpt'] : ''); ?>
    <?php if ( $card_text ) : ?>
    <p class="sk-spg-card-excerpt"><?php echo esc_html( $card_text ); ?></p>
    <?php endif; ?>
    <a href="<?php echo esc_url( $s['url'] ); ?>" class="sk-spg-read-link">
      <?php echo esc_html( $read_more_label ); ?>
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
  </div>
</article>
