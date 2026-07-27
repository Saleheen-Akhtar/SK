<?php
/**
 * Template Name: Stories Archive
 *
 * Redesigned to match img3:
 * - Two-column hero header (text left, woman image right)
 * - "Real Journeys | Heartfelt Transformations | Lasting Impact" badge pills
 * - Filter pills + "Newest First" sort
 * - 4-column card grid (cover photo, quote badge, category, title, excerpt, "Read Her Story →")
 * - CTA banner at bottom: "Your story can inspire change. Share Your Story"
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

/* Hero image — editable via Settings or fallback */
$hero_img = sk_option( 'stories_page_hero_image', '' );
?>

<main class="sk-stories-pg" id="stories-archive">

  <!-- ══ HERO MASTHEAD: text left, image right ══ -->
  <?php get_template_part('template-parts/stories/hero'); ?>

  <?php if ( ! $has_stories ) : ?>
  <div class="wrap sk-stories-empty" style="padding: 5rem 0; text-align: center;">
    <p><?php echo esc_html(sk_option('sk_stories_no_results', 'No stories in this category yet.')); ?></p>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary" style="margin-top:1.5rem">Back to Home</a>
  </div>

  <?php else : ?>

  <!-- ══ FILTER BAR ══ -->
  <div class="sk-spg-filter-wrap">
    <div class="wrap sk-spg-filter-inner">
      <nav class="sk-spg-filter" aria-label="Filter by category">
        <button class="sk-spg-filter-btn active" data-filter="all">All Stories</button>
        <?php foreach ( $categories as $cat ) : ?>
        <button class="sk-spg-filter-btn" data-filter="<?php echo esc_attr( sanitize_title( $cat ) ); ?>"><?php echo esc_html( $cat ); ?></button>
        <?php endforeach; ?>
      </nav>
      <div class="sk-spg-filter-right">
        <!-- Real-time Search -->
        <div class="sk-spg-search-wrap">
          <input type="text" id="sk-stories-search" class="sk-spg-search-input" placeholder="Search stories..." aria-label="Search stories" />
          <span class="sk-spg-search-icon" aria-hidden="true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
          </span>
        </div>

        <!-- Sort Dropdown -->
        <div class="sk-spg-sort-container">
          <button class="sk-spg-sort" id="sort-trigger" aria-haspopup="listbox" aria-expanded="false" aria-label="Sort stories">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><line x1="21" y1="10" x2="7" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="7" y2="18"/></svg>
            <span id="sort-label">Newest First</span>
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <ul class="sk-spg-sort-dropdown" role="listbox" aria-labelledby="sort-trigger" id="sort-dropdown" style="display:none">
            <li class="sk-spg-sort-option active" role="option" data-value="newest" aria-selected="true">Newest First</li>
            <li class="sk-spg-sort-option" role="option" data-value="oldest" aria-selected="false">Oldest First</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- ══ STORY CARDS GRID ══ -->
  <div class="wrap sk-spg-body">
    <div class="sk-spg-grid" id="sk-spg-grid">
      <?php foreach ( $all_stories as $s ) : ?>
        <?php get_template_part('template-parts/stories/card', null, ['story' => $s]); ?>
      <?php endforeach; ?>
    </div>

    <!-- No results message -->
    <div class="sk-spg-no-results" id="sk-spg-no-results" style="display:none">
      <p><?php echo esc_html(sk_option('sk_stories_no_results', 'No stories in this category yet.')); ?></p>
    </div>
  </div>

  <!-- ══ SHARE YOUR STORY CTA BANNER ══ -->
  <?php get_template_part('template-parts/stories/cta'); ?>

  <?php endif; ?>

</main>

<?php get_footer(); ?>

<!-- Stories filter script enqueued via setup.php -->
