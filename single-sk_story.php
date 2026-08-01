<?php
/**
 * Single Story (Podium Style)
 */
get_header();

if ( have_posts() ) :
  while ( have_posts() ) :
    the_post();
    $id           = get_the_ID();
    $category     = get_post_meta( $id, 'story_category',        true );
    $author_name = get_post_meta( $id, 'story_author_name', true );
    if ( ! $author_name ) {
        $disp = get_the_author_meta('display_name');
        $author_name = ( $disp && ! str_contains( strtolower( $disp ), 'saleheen' ) && ! str_contains( strtolower( $disp ), 'admin' ) ) ? $disp : 'Anonymous';
    }
    $cover        = get_post_meta( $id, 'story_cover_image_url', true ) ?: ( has_post_thumbnail() ? get_the_post_thumbnail_url( $id, 'full' ) : '' );
    $read_time    = get_post_meta( $id, 'story_read_time',       true );
?>

<article class="sk-story-single" id="story-<?php echo $id; ?>" style="background-color:var(--color-surface-base); min-height:100vh;">

  <!-- Hero cover -->
  <?php if ( $cover ) : ?>
  <div class="sk-story-single__cover wrap" style="padding-top:var(--space-2);">
    <div style="aspect-ratio:16/9; overflow:hidden;">
        <img src="<?php echo esc_url( $cover ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="eager" style="width:100%; height:100%; object-fit:cover;" />
    </div>
  </div>
  <?php endif; ?>

  <!-- Story header -->
  <header class="sk-story-single__header" style="padding:var(--space-2) 0;">
    <div class="wrap-narrow">
      <div class="sk-post-meta" style="margin-bottom:var(--space-1); display:flex; gap:1rem; align-items:center;">
        <a href="<?php echo esc_url( home_url( '/stories/' ) ); ?>" class="body-small" style="text-decoration:underline;">&larr; All Stories</a>
        <?php if ( $category ) : ?>
        <span class="body-small"><?php echo esc_html( $category ); ?></span>
        <?php endif; ?>
        <?php if ( $read_time ) : ?>
        <span class="body-small"><?php echo esc_html( $read_time ); ?></span>
        <?php endif; ?>
      </div>

      <h1 class="display-h2" style="margin-bottom:var(--space-1);"><?php the_title(); ?></h1>

      <div class="body-small" style="text-transform:uppercase;">
        <span>By <?php echo esc_html( $author_name ); ?></span>
        <span style="margin:0 0.5rem;">&middot;</span>
        <span><?php echo get_the_date( 'F j, Y' ); ?></span>
      </div>
    </div>
  </header>

  <!-- Body -->
  <div class="sk-story-single__body" style="padding-bottom:var(--space-3);">
    <div class="wrap-narrow body-ui">
      <?php the_content(); ?>
    </div>
  </div>

</article>
<?php
  endwhile;
endif;
get_footer();
?>
