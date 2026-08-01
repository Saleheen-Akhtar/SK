<?php
/**
 * single.php — Sacred Kompass blog post (Podium Style)
 */

if (!is_singular('post')) {
    get_template_part('page');
    return;
}

get_header();
the_post();

$post_id    = get_the_ID();
$cats       = get_the_category($post_id);
$primary    = $cats ? $cats[0] : null;
$read_time  = sk_reading_time( $post_id );
$thumb      = get_the_post_thumbnail_url($post_id, 'full');
$blog_url   = home_url('/journal/');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('sk-post sk-podium-post'); ?> itemscope itemtype="https://schema.org/BlogPosting">

  <header class="sk-post-hero" style="padding-top:var(--space-3); padding-bottom:var(--space-2); background-color:var(--color-surface-base);">
    <div class="wrap-narrow">

      <div class="sk-post-meta" style="margin-bottom:var(--space-1); display:flex; gap:1rem; align-items:center;">
        <a href="<?php echo esc_url($blog_url); ?>" class="body-small" style="text-decoration:underline;">&larr; Journal</a>
        <?php if ($primary) : ?>
          <a href="<?php echo esc_url(add_query_arg('cat', $primary->slug, $blog_url)); ?>" class="body-small"><?php echo esc_html($primary->name); ?></a>
        <?php endif; ?>
        <span class="body-small"><?php echo esc_html($read_time); ?></span>
        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="body-small" itemprop="datePublished"><?php echo get_the_date('F j, Y'); ?></time>
      </div>

      <h1 class="display-impact" itemprop="headline"><?php the_title(); ?></h1>

      <?php if (has_excerpt()) : ?>
        <p class="body-serif" style="margin-top:var(--space-1); font-size:var(--font-size-lg);" itemprop="description"><?php the_excerpt(); ?></p>
      <?php endif; ?>

    </div>

    <?php if ($thumb) : ?>
      <div class="sk-post-hero-img wrap" style="margin-top:var(--space-2);">
        <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" itemprop="image" loading="eager" style="width:100%; height:auto;">
      </div>
    <?php endif; ?>
  </header>

  <div class="sk-post-body wrap-narrow body-ui" itemprop="articleBody" style="padding-bottom:var(--space-3);">
    <?php the_content(); ?>
  </div>

</article>

<?php get_footer(); ?>
