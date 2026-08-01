<?php
/**
 * Template Name: Stories Archive (Podium Style)
 */
get_header();

$all_stories_query = new WP_Query( [
    'post_type'      => 'sk_story',
    'post_status'    => 'publish',
    'posts_per_page' => 50,
    'orderby'        => [ 'date' => 'DESC' ],
    'no_found_rows'  => true,
] );

$all_stories = [];
$categories  = [];

if ( $all_stories_query->have_posts() ) {
    while ( $all_stories_query->have_posts() ) {
        $all_stories_query->the_post();
        $id  = get_the_ID();
        $cat = get_post_meta( $id, 'story_category', true );
        if ( $cat && ! in_array( $cat, $categories, true ) ) {
            $categories[] = $cat;
        }

        $cover_id  = (int) get_post_meta( $id, 'story_cover_image_id', true );
        $cover_url = $cover_id
            ? wp_get_attachment_image_url( $cover_id, 'large' )
            : get_post_meta( $id, 'story_cover_image_url', true );
        if ( ! $cover_url && has_post_thumbnail() ) {
            $cover_url = get_the_post_thumbnail_url( $id, 'large' );
        }

        $author_name = get_post_meta( $id, 'story_author_name', true );
        if ( ! $author_name ) {
            $disp = get_the_author_meta('display_name');
            $author_name = ( $disp && ! str_contains( strtolower( $disp ), 'saleheen' ) && ! str_contains( strtolower( $disp ), 'admin' ) ) ? $disp : 'Anonymous';
        }

        $all_stories[] = [
            'id'           => $id,
            'title'        => get_the_title(),
            'pull_quote'   => get_post_meta( $id, 'story_pull_quote',      true ),
            'excerpt'      => wp_trim_words( get_the_excerpt() ?: strip_tags( get_the_content() ), 22, '…' ),
            'category'     => $cat,
            'author_name'  => $author_name,
            'author_location' => get_post_meta( $id, 'story_author_location', true ),
            'cover'        => $cover_url,
            'read_time'    => get_post_meta( $id, 'story_read_time',    true ),
            'date'         => get_the_date( 'M Y' ),
            'url'          => get_permalink(),
            'featured'     => (bool) get_post_meta( $id, 'story_featured', true ),
        ];
    }
    wp_reset_postdata();
}

$has_stories = ! empty( $all_stories );
?>

<main class="sk-stories-pg" id="stories-archive" style="background-color:var(--color-surface-base); min-height:100vh;">

  <section class="sk-stories-hero" style="padding:var(--space-3) 0 var(--space-2);">
    <div class="wrap">
      <span class="eyebrow">Journeys</span>
      <h1 class="display-impact">Stories of Transformation</h1>
      <p class="body-serif" style="margin-top:var(--space-1); max-width:800px;">
        Real experiences from the community.
      </p>
    </div>
  </section>

  <?php if ( ! $has_stories ) : ?>
  <div class="wrap sk-stories-empty" style="padding:var(--space-2) 0;">
    <p class="body-ui">No stories published yet.</p>
  </div>
  <?php else : ?>

  <!-- Grid -->
  <div class="wrap" style="padding-bottom:var(--space-3);">
    <div class="sk-stories-grid" id="sk-spg-grid" style="display:grid; grid-template-columns:1fr; gap:var(--space-2);">
      <?php foreach ( $all_stories as $s ) : ?>
        <a href="<?php echo esc_url($s['url']); ?>" class="sk-story-card klimt-hover-reveal" data-category="<?php echo esc_attr(sanitize_title($s['category'])); ?>" style="display:block; text-decoration:none;">
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
  <?php endif; ?>

</main>

<style>
@media (min-width: 768px) {
  .sk-stories-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (min-width: 1024px) {
  .sk-stories-grid { grid-template-columns: repeat(3, 1fr) !important; }
}
</style>

<?php get_footer(); ?>
