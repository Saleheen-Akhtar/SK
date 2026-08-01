<?php
/**
 * Archive template for sk_event CPT (Podium Style)
 */
get_header();

$today = date('Y-m-d');

// Upcoming events
$upcoming = new WP_Query([
    'post_type'      => 'sk_event',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => 'event_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [[
        'key'     => 'event_date',
        'value'   => $today,
        'compare' => '>=',
        'type'    => 'DATE',
    ]],
]);

// Past events
$past = new WP_Query([
    'post_type'      => 'sk_event',
    'post_status'    => 'publish',
    'posts_per_page' => 9,
    'meta_key'       => 'event_date',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'meta_query'     => [[
        'key'     => 'event_date',
        'value'   => $today,
        'compare' => '<',
        'type'    => 'DATE',
    ]],
]);

function sk_render_event_card( WP_Post $post, bool $is_past = false ): void {
    $id           = $post->ID;
    $date         = get_post_meta( $id, 'event_date',        true );
    $time         = get_post_meta( $id, 'event_time',        true );
    $location     = get_post_meta( $id, 'event_location',    true );
    $format       = get_post_meta( $id, 'event_format',      true ) ?: 'inperson';
    $price        = get_post_meta( $id, 'event_price',       true );
    $reg_url      = get_post_meta( $id, 'event_reg_url',     true );
    $description  = get_post_meta( $id, 'event_description', true );
    $tag          = get_post_meta( $id, 'event_tag',         true );
    $sold_out     = (bool) get_post_meta( $id, 'event_sold_out', true );
    $thumb        = get_the_post_thumbnail_url( $id, 'large' );
    $permalink    = get_permalink( $id );
    $title        = get_the_title( $id );

    $date_fmt  = $date ? date_i18n( 'D, j M Y', strtotime( $date ) ) : '';
    $format_label = match( $format ) {
        'online'  => 'Online',
        'hybrid'  => 'Hybrid',
        default   => 'In Person',
    };
    ?>
    <article class="sk-event-card<?php echo $is_past ? ' is-past' : ''; ?> klimt-hover-reveal" style="display:block; text-decoration:none; margin-bottom:var(--space-2);">
      <?php if ( $thumb ) : ?>
        <a href="<?php echo esc_url( $permalink ); ?>" class="klimt-image-wrapper" tabindex="-1" aria-hidden="true" style="aspect-ratio:16/9; display:block; margin-bottom:var(--space-1);">
          <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
        </a>
      <?php endif; ?>

      <div class="sk-event-card__body body-ui">
        <div class="sk-event-card__meta" style="display:flex; gap:1rem; align-items:center; margin-bottom:0.5rem; font-size:var(--font-size-sm); text-transform:uppercase;">
          <?php if ( $date ) : ?>
            <span><?php echo esc_html( $date_fmt ); ?></span>
          <?php endif; ?>
          <span style="color:var(--color-text-tertiary);"><?php echo esc_html( $format_label ); ?></span>
          <?php if ( $sold_out ) : ?>
            <span style="color:var(--color-text-tertiary);"><?php esc_html_e( 'Sold Out', 'sacred-kompass' ); ?></span>
          <?php endif; ?>
        </div>

        <h3 class="display-h3" style="margin-bottom:0.5rem;">
          <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
        </h3>

        <?php if ( $description ) : ?>
          <p class="body-serif" style="margin-bottom:1rem;"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>

        <div class="sk-event-card__details body-small" style="display:flex; flex-direction:column; gap:0.25rem;">
          <?php if ( $time ) : ?>
            <span>Time: <?php echo esc_html( date_i18n( 'g:i A', strtotime( $time ) ) ); ?></span>
          <?php endif; ?>
          <?php if ( $location && $format !== 'online' ) : ?>
            <span>Location: <?php echo esc_html( $location ); ?></span>
          <?php endif; ?>
          <?php if ( $price ) : ?>
            <span>Investment: <?php echo esc_html( $price ); ?></span>
          <?php endif; ?>
        </div>

        <?php if ( ! $is_past && $reg_url && ! $sold_out ) : ?>
          <a href="<?php echo esc_url( $reg_url ); ?>" class="btn-outline" target="_blank" rel="noopener" style="margin-top:1rem; display:inline-block;">
            <?php esc_html_e( 'Reserve Your Spot', 'sacred-kompass' ); ?>
          </a>
        <?php endif; ?>
      </div>
    </article>
    <?php
}
?>

<div class="sk-events-page" style="background-color:var(--color-surface-base); min-height:100vh;">

  <div class="sk-events-hero wrap" style="padding:var(--space-3) 0 var(--space-2);">
    <span class="eyebrow"><?php esc_html_e( 'Gatherings', 'sacred-kompass' ); ?></span>
    <h1 class="display-impact"><?php esc_html_e( 'Upcoming Events', 'sacred-kompass' ); ?></h1>
    <p class="body-serif" style="margin-top:var(--space-1); max-width:800px;">
      Join us in person or online for immersive experiences rooted in ancient wisdom.
    </p>
  </div>

  <div class="wrap sk-events-wrap" style="padding-bottom:var(--space-3);">

    <!-- Upcoming -->
    <?php if ( $upcoming->have_posts() ) : ?>
      <div class="sk-events-grid" id="upcoming-events" style="display:grid; grid-template-columns:1fr; gap:var(--space-2);">
        <?php while ( $upcoming->have_posts() ) : $upcoming->the_post(); ?>
          <?php sk_render_event_card( get_post() ); ?>
        <?php endwhile; ?>
      </div>
      <?php wp_reset_postdata(); ?>
    <?php else : ?>
      <div class="sk-events-empty body-ui" style="padding:var(--space-2) 0;">
        <p><?php esc_html_e( 'No upcoming events at this time.', 'sacred-kompass' ); ?></p>
      </div>
    <?php endif; ?>

    <!-- Past Events -->
    <?php if ( $past->have_posts() ) : ?>
      <div class="sk-events-past-divider" style="margin-top:var(--space-3); padding-top:var(--space-2); border-top:1px solid var(--color-surface-strong);">
        <h2 class="display-h3" style="margin-bottom:var(--space-2);"><?php esc_html_e( 'Past Events', 'sacred-kompass' ); ?></h2>
      </div>
      <div class="sk-events-grid sk-events-grid--past" style="display:grid; grid-template-columns:1fr; gap:var(--space-2); opacity:0.7;">
        <?php while ( $past->have_posts() ) : $past->the_post(); ?>
          <?php sk_render_event_card( get_post(), true ); ?>
        <?php endwhile; ?>
      </div>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>

  </div>
</div>

<style>
@media (min-width: 768px) {
  .sk-events-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
</style>

<?php get_footer(); ?>
