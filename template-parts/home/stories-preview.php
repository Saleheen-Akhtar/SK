<?php
/**
 * Stories Preview — Homepage Section (Podium Style)
 */
$sp_eyebrow    = sk_option( 'stories_preview_eyebrow',  'Journeys' );
$sp_heading    = sk_option( 'stories_preview_heading',  'Stories of Transformation' );
$sp_sub        = sk_option( 'stories_preview_sub', 'Real experiences from the community.' );

$stories_page_id = sk_get_page_id_by_path( 'stories' );
$stories_url  = $stories_page_id ? get_permalink( $stories_page_id ) : home_url( '/stories/' );

$stories = get_transient( 'sk_stories_preview_data_v4' );
if ( false === $stories ) {
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
            $story_posts[] = get_post();
            $exclude_ids[] = get_the_ID();
        }
    }
    wp_reset_postdata();

    if ( count( $story_posts ) < 3 ) {
        $recent_query = new WP_Query( [
            'post_type'           => 'sk_story',
            'post_status'         => 'publish',
            'posts_per_page'      => 3 - count( $story_posts ),
            'post__not_in'        => $exclude_ids,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
        ] );

        if ( $recent_query->have_posts() ) {
            while ( $recent_query->have_posts() ) {
                $recent_query->the_post();
                $story_posts[] = get_post();
            }
        }
        wp_reset_postdata();
    }

    $stories = [];
    foreach ( $story_posts as $sp ) {
        $id = $sp->ID;
        $cover_id  = (int) get_post_meta( $id, 'story_cover_image_id', true );
        $cover_url = $cover_id ? wp_get_attachment_image_url( $cover_id, 'large' ) : get_post_meta( $id, 'story_cover_image_url', true );
        if ( ! $cover_url && has_post_thumbnail( $id ) ) {
            $cover_url = get_the_post_thumbnail_url( $id, 'large' );
        }

        $author_name = get_post_meta( $id, 'story_author_name', true );
        if ( ! $author_name ) {
            $disp = get_the_author_meta('display_name', $sp->post_author);
            $author_name = ( $disp && ! str_contains( strtolower( $disp ), 'saleheen' ) && ! str_contains( strtolower( $disp ), 'admin' ) ) ? $disp : 'Anonymous';
        }

        $stories[] = [
            'id'          => $id,
            'title'       => get_the_title( $id ),
            'excerpt'     => wp_trim_words( get_the_excerpt( $sp ) ?: strip_tags( $sp->post_content ), 22, '…' ),
            'category'    => get_post_meta( $id, 'story_category', true ),
            'author_name' => $author_name,
            'cover'       => $cover_url,
            'url'         => get_permalink( $id ),
        ];
    }
    set_transient( 'sk_stories_preview_data_v4', $stories, 12 * HOUR_IN_SECONDS );
}
?>

<section id="stories" class="sk-podium-stories section-py" style="background-color:var(--color-surface-base); border-top:1px solid var(--color-surface-strong);">
  <div class="wrap stagger-children">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:var(--space-2);">
      <div>
        <span class="eyebrow"><?php echo esc_html($sp_eyebrow); ?></span>
        <h2 class="display-h3"><?php echo esc_html($sp_heading); ?></h2>
        <p class="body-serif" style="margin-top:0.5rem; max-width:600px;"><?php echo esc_html($sp_sub); ?></p>
      </div>
      <div>
        <a href="<?php echo esc_url($stories_url); ?>" class="btn-outline">View All</a>
      </div>
    </div>

    <div class="sk-stories-grid" style="display:grid; grid-template-columns:1fr; gap:var(--space-2);">
      <?php foreach ( $stories as $s ) : ?>
        <a href="<?php echo esc_url($s['url']); ?>" class="sk-story-card klimt-hover-reveal" style="display:block; text-decoration:none;">
          <div class="klimt-image-wrapper" style="aspect-ratio:3/4;">
            <?php if ($s['cover']): ?>
              <img src="<?php echo esc_url($s['cover']); ?>" alt="<?php echo esc_attr($s['title']); ?>" />
            <?php else: ?>
              <div class="placeholder-img"></div>
            <?php endif; ?>
          </div>
          <div class="sk-story-meta" style="margin-top:var(--space-1);">
            <?php if ($s['category']): ?>
              <span class="body-small" style="text-transform:uppercase; display:block; margin-bottom:0.5rem;"><?php echo esc_html($s['category']); ?></span>
            <?php endif; ?>
            <h3 class="body-ui" style="margin-bottom:0.5rem;"><?php echo esc_html($s['title']); ?></h3>
            <p class="body-serif" style="font-size:var(--font-size-sm);"><?php echo esc_html($s['excerpt']); ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<style>
@media (min-width: 768px) {
  .sk-podium-stories .sk-stories-grid { grid-template-columns: repeat(3, 1fr) !important; }
}
</style>
