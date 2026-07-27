<?php
/**
 * Stories Preview — Homepage Section
 * Layout: Full-bleed bg image.
 * Top: eyebrow + heading + sub + CTA button (left-aligned, ~40% width).
 * Bottom: horizontal row of 3 cards sliding across full width.
 * Each card: [quote icon + italic text + divider + author avatar/name/cat] left | [cover photo] right.
 * Prev/Next arrows on outer edges. Dots centered below.
 */

$sp_bg_image        = sk_option( 'stories_preview_bg_image',  '' );
$sp_bg_image_mobile = sk_option( 'stories_preview_bg_image_mobile', '' );
$sp_eyebrow    = sk_option( 'stories_preview_eyebrow',  '' );
$sp_heading    = sk_option( 'stories_preview_heading',  '' );
$sp_sub        = sk_option( 'stories_preview_sub', '' );

/* ── stories page URL ── */
$stories_page_id = sk_get_page_id_by_path( 'stories' );
$stories_url  = $stories_page_id ? get_permalink( $stories_page_id ) : home_url( '/stories/' );

/* ── Query sk_story posts ── */
$stories = get_transient( 'sk_stories_preview_data_v4' );
if ( false === $stories ) {
    // 1. Query featured stories first
    $featured_query = new WP_Query( [
        'post_type'           => 'sk_story',
        'post_status'         => 'publish',
        'posts_per_page'      => 3,
        'meta_key'            => 'story_featured',
        'meta_value'          => '1',
        'orderby'             => 'menu_order',
        'order'               => 'ASC',
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
    ] );

    $story_posts = [];
    $exclude_ids = [];

    if ( $featured_query->have_posts() ) {
        while ( $featured_query->have_posts() ) {
            $featured_query->the_post();
            $pid = get_the_ID();
            $story_posts[] = $pid;
            $exclude_ids[] = $pid;
        }
        wp_reset_postdata();
    }

    // 2. If less than 3, fill with regular stories
    $needed = 3 - count( $story_posts );
    if ( $needed > 0 ) {
        $args = [
            'post_type'           => 'sk_story',
            'post_status'         => 'publish',
            'posts_per_page'      => $needed,
            'orderby'             => 'menu_order',
            'order'               => 'ASC',
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
        ];
        if ( ! empty( $exclude_ids ) ) {
            $args['post__not_in'] = $exclude_ids;
        }
        $regular_query = new WP_Query( $args );
        if ( $regular_query->have_posts() ) {
            while ( $regular_query->have_posts() ) {
                $regular_query->the_post();
                $story_posts[] = get_the_ID();
            }
            wp_reset_postdata();
        }
    }

    // 3. Populate stories data array
    $stories = [];
    foreach ( $story_posts as $pid ) {
        $cover_id  = (int) get_post_meta( $pid, 'story_cover_image_id', true );
        $cover_url = $cover_id
            ? wp_get_attachment_image_url( $cover_id, 'medium_large' )
            : get_post_meta( $pid, 'story_cover_image_url', true );
        if ( ! $cover_url && has_post_thumbnail( $pid ) ) {
            $cover_url = get_the_post_thumbnail_url( $pid, 'medium_large' );
        }

        $author_name = get_post_meta( $pid, 'story_author_name', true );
        if ( ! $author_name ) {
            $p_obj = get_post( $pid );
            $disp = get_the_author_meta('display_name', $p_obj ? $p_obj->post_author : 0);
            $author_name = ( $disp && ! str_contains( strtolower( $disp ), 'saleheen' ) && ! str_contains( strtolower( $disp ), 'admin' ) ) ? $disp : 'Anonymous';
        }
        $initials = '';
        foreach ( explode( ' ', trim( $author_name ) ) as $w ) {
            $initials .= mb_strtoupper( mb_substr( $w, 0, 1 ) );
            if ( strlen( $initials ) >= 2 ) break;
        }

        $raw_excerpt = get_post_meta( $pid, 'story_pull_quote', true );
        $post_excerpt = '';
        $p_obj = get_post( $pid );
        $post_content = $p_obj ? $p_obj->post_content : '';
        if ( $post_content ) {
            $stripped = wp_strip_all_tags( $post_content );
            $words    = explode( ' ', $stripped );
            $post_excerpt = implode( ' ', array_slice( $words, 0, 22 ) );
            if ( count( $words ) > 22 ) $post_excerpt .= '…';
        }

        $stories[] = [
            'id'           => $pid,
            'pull_quote'   => get_post_meta( $pid, 'story_pull_quote', true ),
            'excerpt'      => $post_excerpt,
            'category'     => get_post_meta( $pid, 'story_category',   true ),
            'author_name'  => $author_name,
            'author_location' => get_post_meta( $pid, 'story_author_location', true ),
            'initials'     => $initials,
            'cover'        => $cover_url,
            'permalink'    => get_permalink($pid),
        ];
    }

    set_transient( 'sk_stories_preview_data_v4', $stories, 12 * HOUR_IN_SECONDS );
}

if ( empty( $stories ) ) { return; }

$all_cards = [];
foreach ( $stories as $s ) {
    $all_cards[] = $s;
}
$all_cards[] = 'view_more';
$pairs = array_chunk( $all_cards, 2 );
$slider_total = count( $all_cards );

$sp_styles = [];
if ($sp_bg_image) {
    $sp_styles[] = '--sp-bg:url(\'' . esc_url($sp_bg_image) . '\')';
}
if ($sp_bg_image_mobile) {
    $sp_styles[] = '--sp-bg-mobile:url(\'' . esc_url($sp_bg_image_mobile) . '\')';
}
$bg_attr = !empty($sp_styles) ? ' style="' . implode(';', $sp_styles) . ';"' : '';
?>


<section
  class="sk-stories-preview-section sk-sp-v4"
  id="stories-preview"
  aria-labelledby="sp-heading"
  <?php echo $bg_attr; ?>
>


  <div class="sk-sp-v4-layout">

    <!-- ── TOP: heading block ── -->
    <div class="sk-sp-v4-top reveal">
      <div class="sk-sp-v4-top-inner">
        <?php if ($sp_eyebrow) : ?>
        <p class="sk-sp-v4-eyebrow"><?php echo esc_html($sp_eyebrow); ?></p>
        <?php endif; ?>
        <?php if ($sp_heading) : ?>
        <h2 class="sk-sp-v4-heading" id="sp-heading">
          <?php echo wp_kses( $sp_heading, [
              'em'     => [],
              'strong' => [],
              'span'   => [],
              'br'     => [],
          ] ); ?>
        </h2>
        <?php endif; ?>
        
        <!-- Compass star divider removed -->

        <?php if ($sp_sub) : ?>
        <p class="sk-sp-v4-sub"><?php echo esc_html($sp_sub); ?></p>
        <?php endif; ?>
      </div>
    </div>

    <!-- ── BOTTOM: slider ── -->
    <div class="sk-sp-v4-bottom">

      <div class="sk-sp-v4-cols-wrap">
        
        <!-- LEFT SLIDER CONTAINER -->
        <div class="sk-sp-v4-slider-container sk-sp-v4-slider-left" data-start-index="0">
          <!-- Arrow prev -->
          <button class="sk-sp-v4-arrow sk-sp-v4-prev" aria-label="Previous story">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M11 4L6 9L11 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>

          <!-- Track -->
          <div class="sk-sp-v4-track-wrap">
            <div class="sk-sp-v4-track">
              <?php foreach ( $all_cards as $s ) : ?>
                <?php if ( $s === 'view_more' ) : ?>
                <!-- View More Stories Card -->
                <a href="<?php echo esc_url( $stories_url ); ?>" class="sk-sp-v4-card sk-sp-v4-viewmore-card" aria-label="View all stories">
                  <div class="sk-sp-v4-card-text sk-sp-v4-viewmore-text-col">
                    <div class="sk-sp-v4-viewmore-star" aria-hidden="true">
                      <svg width="26" height="26" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 0 C11.5 7 11.5 10 14 14 C16.5 10 16.5 7 14 0Z" fill="currentColor"/>
                        <path d="M14 28 C11.5 21 11.5 18 14 14 C16.5 18 16.5 21 14 28Z" fill="currentColor"/>
                        <path d="M0 14 C7 11.5 10 11.5 14 14 C10 16.5 7 16.5 0 14Z" fill="currentColor"/>
                        <path d="M28 14 C21 11.5 18 11.5 14 14 C18 16.5 21 16.5 28 14Z" fill="currentColor"/>
                      </svg>
                    </div>
                    <h3 class="sk-sp-v4-viewmore-title">Explore More Journeys</h3>
                    <p class="sk-sp-v4-viewmore-desc">Discover more heartfelt transformations and lasting impact from our collective.</p>
                    <span class="sk-sp-v4-viewmore-link">
                      View All Stories
                      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                  </div>
                  <div class="sk-sp-v4-card-photo sk-sp-v4-viewmore-photo-col">
                    <div class="sk-sp-v4-viewmore-gradient">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12H19M19 12L12 5M19 12L12 19"/>
                      </svg>
                    </div>
                  </div>
                </a>
                <?php else : ?>
                <!-- Regular Card -->
                <a href="<?php echo esc_url( $s['permalink'] ); ?>" class="sk-sp-v4-card" aria-label="<?php echo esc_attr( $s['author_name'] ); ?>&#8217;s story">
                  <div class="sk-sp-v4-card-text">
                    <div class="sk-sp-v4-qicon" aria-hidden="true">
                      <svg width="26" height="20" viewBox="0 0 26 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 20V12C0 8.8 1.01 6.06 3.04 3.76C5.07 1.45 7.77 0.05 11.2 0V3.54C9.31 3.86 7.77 4.73 6.55 6.15C5.33 7.56 4.72 9.15 4.72 10.93H10.4V20H0ZM15.6 20V12C15.6 8.8 16.61 6.06 18.64 3.76C20.67 1.45 23.37 0.05 26.8 0V3.54C24.91 3.86 23.37 4.73 22.15 6.15C20.93 7.56 20.32 9.15 20.32 10.93H26V20H15.6Z" fill="currentColor"/>
                      </svg>
                    </div>
                    <?php $qt = $s['pull_quote']; $ex = $s['excerpt']; ?>
                    <?php if ( $qt ) : ?>
                    <p class="sk-sp-v4-qtext"><em><?php echo esc_html( $qt ); ?></em></p>
                    <?php elseif ( $ex ) : ?>
                    <p class="sk-sp-v4-qtext"><em><?php echo esc_html( $ex ); ?></em></p>
                    <?php endif; ?>
                    <?php if ( $ex && ! $qt ) : ?>
                    <span class="sk-sp-v4-readmore">read story</span>
                    <?php elseif ( $qt ) : ?>
                    <span class="sk-sp-v4-readmore">read story</span>
                    <?php endif; ?>
                    <div class="sk-sp-v4-qdivider" aria-hidden="true"></div>
                    <div class="sk-sp-v4-author">
                      <div class="sk-sp-v4-avatar" aria-hidden="true"><?php echo esc_html( $s['initials'] ); ?></div>
                      <div class="sk-sp-v4-ainfo">
                        <span class="sk-sp-v4-aname"><?php echo esc_html( $s['author_name'] ); ?></span>
                        <?php if ( $s['category'] || ! empty( $s['author_location'] ) ) : ?>
                        <span class="sk-sp-v4-acat">
                          <?php
                          $meta_parts = [];
                          if ( $s['category'] ) $meta_parts[] = esc_html( $s['category'] );
                          if ( ! empty( $s['author_location'] ) ) $meta_parts[] = esc_html( $s['author_location'] );
                          echo implode( ' · ', $meta_parts );
                          ?>
                        </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <div class="sk-sp-v4-card-photo">
                    <?php if ( $s['cover'] ) : ?>
                    <img src="<?php echo esc_url( $s['cover'] ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s’s story cover', 'sacred-kompass' ), $s['author_name'] ) ); ?>" loading="lazy" width="220" height="280" />
                    <?php else : ?>
                    <div class="sk-sp-v4-photo-placeholder" aria-hidden="true">
                      <span><?php echo esc_html( mb_substr( $s['author_name'], 0, 1 ) ); ?></span>
                    </div>
                    <?php endif; ?>
                  </div>
                </a>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Arrow next -->
          <button class="sk-sp-v4-arrow sk-sp-v4-next" aria-label="Next story">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M7 4L12 9L7 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>

        <!-- CENTER SPACER (Desktop title clear space) -->
        <div class="sk-sp-v4-center-spacer"></div>

        <!-- RIGHT SLIDER CONTAINER -->
        <div class="sk-sp-v4-slider-container sk-sp-v4-slider-right" data-start-index="1">
          <!-- Arrow prev -->
          <button class="sk-sp-v4-arrow sk-sp-v4-prev" aria-label="Previous story">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M11 4L6 9L11 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>

          <!-- Track -->
          <div class="sk-sp-v4-track-wrap">
            <div class="sk-sp-v4-track">
              <?php foreach ( $all_cards as $s ) : ?>
                <?php if ( $s === 'view_more' ) : ?>
                <!-- View More Stories Card -->
                <a href="<?php echo esc_url( $stories_url ); ?>" class="sk-sp-v4-card sk-sp-v4-viewmore-card" aria-label="View all stories">
                  <div class="sk-sp-v4-card-text sk-sp-v4-viewmore-text-col">
                    <div class="sk-sp-v4-viewmore-star" aria-hidden="true">
                      <svg width="26" height="26" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 0 C11.5 7 11.5 10 14 14 C16.5 10 16.5 7 14 0Z" fill="currentColor"/>
                        <path d="M14 28 C11.5 21 11.5 18 14 14 C16.5 18 16.5 21 14 28Z" fill="currentColor"/>
                        <path d="M0 14 C7 11.5 10 11.5 14 14 C10 16.5 7 16.5 0 14Z" fill="currentColor"/>
                        <path d="M28 14 C21 11.5 18 11.5 14 14 C18 16.5 21 16.5 28 14Z" fill="currentColor"/>
                      </svg>
                    </div>
                    <h3 class="sk-sp-v4-viewmore-title">Explore More Journeys</h3>
                    <p class="sk-sp-v4-viewmore-desc">Discover more heartfelt transformations and lasting impact from our collective.</p>
                    <span class="sk-sp-v4-viewmore-link">
                      View All Stories
                      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                  </div>
                  <div class="sk-sp-v4-card-photo sk-sp-v4-viewmore-photo-col">
                    <div class="sk-sp-v4-viewmore-gradient">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12H19M19 12L12 5M19 12L12 19"/>
                      </svg>
                    </div>
                  </div>
                </a>
                <?php else : ?>
                <!-- Regular Card -->
                <a href="<?php echo esc_url( $s['permalink'] ); ?>" class="sk-sp-v4-card" aria-label="<?php echo esc_attr( $s['author_name'] ); ?>&#8217;s story">
                  <div class="sk-sp-v4-card-text">
                    <div class="sk-sp-v4-qicon" aria-hidden="true">
                      <svg width="26" height="20" viewBox="0 0 26 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 20V12C0 8.8 1.01 6.06 3.04 3.76C5.07 1.45 7.77 0.05 11.2 0V3.54C9.31 3.86 7.77 4.73 6.55 6.15C5.33 7.56 4.72 9.15 4.72 10.93H10.4V20H0ZM15.6 20V12C15.6 8.8 16.61 6.06 18.64 3.76C20.67 1.45 23.37 0.05 26.8 0V3.54C24.91 3.86 23.37 4.73 22.15 6.15C20.93 7.56 20.32 9.15 20.32 10.93H26V20H15.6Z" fill="currentColor"/>
                      </svg>
                    </div>
                    <?php $qt = $s['pull_quote']; $ex = $s['excerpt']; ?>
                    <?php if ( $qt ) : ?>
                    <p class="sk-sp-v4-qtext"><em><?php echo esc_html( $qt ); ?></em></p>
                    <?php elseif ( $ex ) : ?>
                    <p class="sk-sp-v4-qtext"><em><?php echo esc_html( $ex ); ?></em></p>
                    <?php endif; ?>
                    <?php if ( $ex && ! $qt ) : ?>
                    <span class="sk-sp-v4-readmore">read story</span>
                    <?php elseif ( $qt ) : ?>
                    <span class="sk-sp-v4-readmore">read story</span>
                    <?php endif; ?>
                    <div class="sk-sp-v4-qdivider" aria-hidden="true"></div>
                    <div class="sk-sp-v4-author">
                      <div class="sk-sp-v4-avatar" aria-hidden="true"><?php echo esc_html( $s['initials'] ); ?></div>
                      <div class="sk-sp-v4-ainfo">
                        <span class="sk-sp-v4-aname"><?php echo esc_html( $s['author_name'] ); ?></span>
                        <?php if ( $s['category'] || ! empty( $s['author_location'] ) ) : ?>
                        <span class="sk-sp-v4-acat">
                          <?php
                          $meta_parts = [];
                          if ( $s['category'] ) $meta_parts[] = esc_html( $s['category'] );
                          if ( ! empty( $s['author_location'] ) ) $meta_parts[] = esc_html( $s['author_location'] );
                          echo implode( ' · ', $meta_parts );
                          ?>
                        </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <div class="sk-sp-v4-card-photo">
                    <?php if ( $s['cover'] ) : ?>
                    <img src="<?php echo esc_url( $s['cover'] ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s’s story cover', 'sacred-kompass' ), $s['author_name'] ) ); ?>" loading="lazy" width="220" height="280" />
                    <?php else : ?>
                    <div class="sk-sp-v4-photo-placeholder" aria-hidden="true">
                      <span><?php echo esc_html( mb_substr( $s['author_name'], 0, 1 ) ); ?></span>
                    </div>
                    <?php endif; ?>
                  </div>
                </a>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Arrow next -->
          <button class="sk-sp-v4-arrow sk-sp-v4-next" aria-label="Next story">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M7 4L12 9L7 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>

      </div>

      <!-- Dots (Hidden but kept for compatibility) -->
      <div class="sk-sp-v4-dots" role="tablist" aria-label="Story slides">
        <?php for ( $d = 0; $d < $slider_total; $d++ ) : ?>
        <button class="sk-sp-v4-dot<?php echo $d === 0 ? ' active' : ''; ?>" data-dot="<?php echo esc_attr( $d ); ?>" role="tab" aria-selected="<?php echo $d === 0 ? 'true' : 'false'; ?>" aria-label="Page <?php echo esc_attr( $d + 1 ); ?>"></button>
        <?php endfor; ?>
      </div>

    </div><!-- /bottom -->

  </div><!-- /layout -->
</section>
