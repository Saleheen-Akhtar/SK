<?php
/**
 * Single Event template (Podium Style)
 */
get_header();

if ( ! have_posts() ) {
    get_footer();
    return;
}

the_post();
$id           = get_the_ID();
$date         = get_post_meta( $id, 'event_date',         true );
$time         = get_post_meta( $id, 'event_time',         true );
$end_time     = get_post_meta( $id, 'event_end_time',     true );
$location     = get_post_meta( $id, 'event_location',     true );
$location_url = get_post_meta( $id, 'event_location_url', true );
$format       = get_post_meta( $id, 'event_format',       true ) ?: 'inperson';
$capacity     = get_post_meta( $id, 'event_capacity',     true );
$price        = get_post_meta( $id, 'event_price',        true );
$reg_url      = get_post_meta( $id, 'event_reg_url',      true );
$description  = get_post_meta( $id, 'event_description',  true );
$tag          = get_post_meta( $id, 'event_tag',          true );
$sold_out     = (bool) get_post_meta( $id, 'event_sold_out', true );
$thumb        = get_the_post_thumbnail_url( $id, 'full' );
$title        = get_the_title();
$content      = get_the_content();

$today        = date( 'Y-m-d' );
$is_past      = $date && $date < $today;
$date_fmt     = $date ? date_i18n( 'l, j F Y', strtotime( $date ) ) : '';
$format_label = match( $format ) {
    'online'  => 'Online',
    'hybrid'  => 'In Person & Online',
    default   => 'In Person',
};
?>

<div class="sk-single-event" style="background-color:var(--color-surface-base); min-height:100vh;">

  <!-- ── Event Hero ── -->
  <div class="sk-event-single-hero wrap" style="padding-top:var(--space-3); padding-bottom:var(--space-2);">
      <a href="<?php echo esc_url( get_post_type_archive_link( 'sk_event' ) ); ?>" class="body-small" style="text-decoration:underline; display:block; margin-bottom:var(--space-1);">
        &larr; <?php esc_html_e( 'All Events', 'sacred-kompass' ); ?>
      </a>

      <?php if ( $tag ) : ?>
        <span class="eyebrow"><?php echo esc_html( $tag ); ?></span>
      <?php endif; ?>
      <h1 class="display-impact"><?php echo esc_html( $title ); ?></h1>

      <?php if ( $is_past ) : ?>
        <span class="body-small" style="background:var(--color-surface-strong); padding:0.2rem 0.6rem; border-radius:4px; display:inline-block; margin-top:0.5rem;"><?php esc_html_e( 'Past Event', 'sacred-kompass' ); ?></span>
      <?php endif; ?>

      <?php if ( $thumb ) : ?>
      <div class="sk-event-single-hero__bg" style="margin-top:var(--space-2);">
        <div style="aspect-ratio:21/9; overflow:hidden;">
          <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr($title); ?>" loading="eager" style="width:100%; height:100%; object-fit:cover;" />
        </div>
      </div>
      <?php endif; ?>
  </div>

  <!-- ── Event Content ── -->
  <div class="wrap sk-event-single-wrap" style="padding-bottom:var(--space-3); display:grid; grid-template-columns:1fr; gap:var(--space-2);">

    <div class="sk-event-single-body body-ui">
      <?php if ( $description ) : ?>
        <p class="body-serif" style="font-size:var(--font-size-lg); margin-bottom:var(--space-2);"><?php echo esc_html( $description ); ?></p>
      <?php endif; ?>
      <?php if ( $content ) : ?>
        <div class="sk-event-single-content">
          <?php echo wp_kses_post( $content ); ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Details Sidebar -->
    <aside class="sk-event-single-sidebar">
      <div class="sk-event-details-card" style="border:1px solid var(--color-surface-strong); padding:var(--space-1);">
        <table style="width:100%; text-align:left; border-collapse:collapse;" class="body-ui">
          <?php if ( $date ) : ?>
          <tr style="border-bottom:1px solid var(--color-surface-strong);">
            <th style="padding:0.5rem 0; font-weight:var(--font-weight-base); color:var(--color-text-tertiary);"><?php esc_html_e( 'Date', 'sacred-kompass' ); ?></th>
            <td style="padding:0.5rem 0; text-align:right;"><?php echo esc_html( $date_fmt ); ?></td>
          </tr>
          <?php endif; ?>

          <?php if ( $time ) : ?>
          <tr style="border-bottom:1px solid var(--color-surface-strong);">
            <th style="padding:0.5rem 0; font-weight:var(--font-weight-base); color:var(--color-text-tertiary);"><?php esc_html_e( 'Time', 'sacred-kompass' ); ?></th>
            <td style="padding:0.5rem 0; text-align:right;">
              <?php echo esc_html( date_i18n( 'g:i A', strtotime( $time ) ) );
                    if ( $end_time ) echo ' – ' . esc_html( date_i18n( 'g:i A', strtotime( $end_time ) ) ); ?>
            </td>
          </tr>
          <?php endif; ?>

          <tr style="border-bottom:1px solid var(--color-surface-strong);">
            <th style="padding:0.5rem 0; font-weight:var(--font-weight-base); color:var(--color-text-tertiary);"><?php esc_html_e( 'Format', 'sacred-kompass' ); ?></th>
            <td style="padding:0.5rem 0; text-align:right;"><?php echo esc_html( $format_label ); ?></td>
          </tr>

          <?php if ( $location && $format !== 'online' ) : ?>
          <tr style="border-bottom:1px solid var(--color-surface-strong);">
            <th style="padding:0.5rem 0; font-weight:var(--font-weight-base); color:var(--color-text-tertiary);"><?php esc_html_e( 'Location', 'sacred-kompass' ); ?></th>
            <td style="padding:0.5rem 0; text-align:right;">
              <?php if ( $location_url ) : ?>
                <a href="<?php echo esc_url( $location_url ); ?>" target="_blank" rel="noopener" style="text-decoration:underline;"><?php echo esc_html( $location ); ?></a>
              <?php else : ?>
                <?php echo esc_html( $location ); ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php endif; ?>

          <?php if ( $price ) : ?>
          <tr style="border-bottom:1px solid var(--color-surface-strong);">
            <th style="padding:0.5rem 0; font-weight:var(--font-weight-base); color:var(--color-text-tertiary);"><?php esc_html_e( 'Investment', 'sacred-kompass' ); ?></th>
            <td style="padding:0.5rem 0; text-align:right;"><?php echo esc_html( $price ); ?></td>
          </tr>
          <?php endif; ?>
        </table>

        <!-- CTA -->
        <?php if ( ! $is_past ) : ?>
          <?php if ( $sold_out ) : ?>
            <div style="margin-top:var(--space-1); text-align:center; padding:0.5rem; background:var(--color-surface-strong);" class="body-small"><?php esc_html_e( 'Fully Booked', 'sacred-kompass' ); ?></div>
          <?php elseif ( $reg_url ) : ?>
            <a href="<?php echo esc_url( $reg_url ); ?>" class="btn-outline" target="_blank" rel="noopener" style="display:block; text-align:center; margin-top:var(--space-1); width:100%;">
              <?php esc_html_e( 'Reserve Spot', 'sacred-kompass' ); ?>
            </a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </aside>

  </div>
</div>

<style>
@media (min-width: 768px) {
  .sk-event-single-wrap { grid-template-columns: 2fr 1fr; }
}
</style>

<?php get_footer(); ?>
