<?php
/**
 * search.php — Sacred Kompass Search Results template
 */
get_header();
$search_query = get_search_query();
?>

<!-- Search Hero -->
<section class="sk-home-hero">
  <div class="wrap sk-home-hero-inner">
    <p class="eyebrow eyebrow-c reveal"><?php esc_html_e('Search Results', 'sacred-kompass'); ?></p>
    <h1 class="display-xl reveal" data-delay="0.12">Search: <em><?php echo esc_html($search_query); ?></em></h1>
  </div>
  <div class="sk-home-hero-ornament" aria-hidden="true">Search</div>
</section>

<!-- Search Form Wrapper -->
<section class="sk-journal-tabs-wrap">
  <div class="wrap">
    <?php get_search_form(); ?>
  </div>
</section>

<!-- Search Results Grid -->
<section class="sk-home-posts-section">
  <div class="wrap">
    <?php if (have_posts()) : ?>
      <div class="sk-blog-grid stagger-children">
        <?php while (have_posts()) : the_post();
          $post_id   = get_the_ID();
          $cats      = get_the_category($post_id);
          $primary   = (is_array($cats) && !empty($cats)) ? $cats[0] : null;
          $read_time = function_exists('sk_reading_time') ? sk_reading_time($post_id) : '1 min read';
          $thumb     = get_the_post_thumbnail_url($post_id, 'large');
          $initial   = mb_substr(get_the_title(), 0, 1);
        ?>
          <article class="sk-blog-card" itemscope itemtype="https://schema.org/BlogPosting">
            <?php if ($thumb) : ?>
              <a href="<?php the_permalink(); ?>" class="sk-blog-card-img-wrap" tabindex="-1" aria-hidden="true">
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" decoding="async" itemprop="image" />
              </a>
            <?php else : ?>
              <a href="<?php the_permalink(); ?>" class="sk-blog-card-img-wrap sk-blog-card-img-placeholder" tabindex="-1" aria-hidden="true">
                <span class="sk-blog-card-placeholder-letter"><?php echo esc_html($initial); ?></span>
              </a>
            <?php endif; ?>
            <div class="sk-blog-card-body">
              <div class="sk-blog-meta">
                <?php if ($primary) : ?>
                  <span class="sk-blog-cat-badge"><?php echo esc_html($primary->name); ?></span>
                <?php endif; ?>
                <span class="sk-blog-read-time">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  <?php echo esc_html($read_time); ?>
                </span>
              </div>
              <h3 class="sk-blog-card-title" itemprop="headline">
                <a href="<?php the_permalink(); ?>" itemprop="url"><?php echo esc_html(get_the_title()); ?></a>
              </h3>
              <p class="sk-blog-card-excerpt" itemprop="description"><?php echo wp_trim_words(get_the_excerpt(), 20, '…'); ?></p>
              <div class="sk-blog-card-foot">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="sk-blog-date" itemprop="datePublished"><?php echo get_the_date('M j, Y'); ?></time>
                <a href="<?php the_permalink(); ?>" class="sk-blog-card-arrow" aria-label="<?php the_title_attribute(); ?>">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>

      <?php if ($GLOBALS['wp_query']->max_num_pages > 1) : ?>
        <nav class="sk-home-pagination reveal" aria-label="Search results pagination">
          <?php echo paginate_links(['total' => $GLOBALS['wp_query']->max_num_pages, 'current' => max(1, get_query_var('paged')), 'prev_text' => '← ' . __('Previous', 'sacred-kompass'), 'next_text' => __('Next', 'sacred-kompass') . ' →']); ?>
        </nav>
      <?php endif; ?>

    <?php else : ?>
      <div class="sk-home-empty reveal">
        <p class="display-h3"><?php esc_html_e('No articles found.', 'sacred-kompass'); ?></p>
        <p class="body-serif"><?php esc_html_e('Try a different search query or explore other pages.', 'sacred-kompass'); ?></p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>
