<?php
/**
 * Journal Preview — Homepage Section (Podium Style)
 */
$journal_heading  = sk_option('journal_preview_heading',  'From the Journal');
$journal_eyebrow  = sk_option('journal_preview_eyebrow',  'Journal');
$journal_url     = home_url('/journal/');

$posts = get_transient('sk_journal_preview_posts');
if (false === $posts) {
  $journal_posts = new WP_Query([
    'post_type'              => 'post',
    'post_status'            => 'publish',
    'posts_per_page'         => 3,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_meta_cache' => true,
    'update_post_term_cache' => true,
    'ignore_sticky_posts'    => true,
  ]);

  $posts = [];
  if ($journal_posts->have_posts()) {
    while ($journal_posts->have_posts()):
      $journal_posts->the_post();
      $post_id = get_the_ID();
      $cats    = get_the_category($post_id);
      $posts[] = [
        'id'        => $post_id,
        'permalink' => get_permalink(),
        'title'     => get_the_title(),
        'excerpt'   => wp_trim_words(get_the_excerpt(), 18, '…'),
        'date'      => get_the_date('M j, Y'),
        'cat'       => $cats ? $cats[0]->name : '',
        'thumb'     => get_the_post_thumbnail_url($post_id, 'large'),
        'read_time' => sk_reading_time($post_id),
      ];
    endwhile;
  }
  wp_reset_postdata();
  set_transient('sk_journal_preview_posts', $posts, HOUR_IN_SECONDS * 6);
}
?>

<?php if (!empty($posts)) : ?>
<section id="journal-preview" class="sk-podium-journal section-py" style="background-color:var(--color-surface-base); border-top:1px solid var(--color-surface-strong);">
  <div class="wrap stagger-children">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:var(--space-2);">
      <div>
        <span class="eyebrow"><?php echo esc_html($journal_eyebrow); ?></span>
        <h2 class="display-h3"><?php echo esc_html($journal_heading); ?></h2>
      </div>
      <div>
        <a href="<?php echo esc_url($journal_url); ?>" class="btn-outline">View All</a>
      </div>
    </div>

    <div class="sk-journal-grid" style="display:grid; grid-template-columns:1fr; gap:var(--space-2);">
      <?php foreach ($posts as $post): ?>
        <a href="<?php echo esc_url($post['permalink']); ?>" class="sk-journal-card klimt-hover-reveal" style="display:block; text-decoration:none;">
          <div class="klimt-image-wrapper" style="aspect-ratio:16/9;">
            <?php if ($post['thumb']): ?>
              <img src="<?php echo esc_url($post['thumb']); ?>" alt="<?php echo esc_attr($post['title']); ?>" />
            <?php else: ?>
              <div class="placeholder-img"></div>
            <?php endif; ?>
          </div>
          <div class="sk-journal-meta" style="margin-top:var(--space-1);">
            <div style="display:flex; gap:1rem; align-items:center; margin-bottom:0.5rem; font-size:var(--font-size-sm); text-transform:uppercase;">
              <?php if ($post['cat']): ?>
                <span style="color:var(--color-text-primary);"><?php echo esc_html($post['cat']); ?></span>
              <?php endif; ?>
              <span style="color:var(--color-text-tertiary);"><?php echo esc_html($post['date']); ?></span>
              <span style="color:var(--color-text-tertiary);"><?php echo esc_html($post['read_time']); ?></span>
            </div>
            <h3 class="body-ui" style="margin-bottom:0.5rem;"><?php echo esc_html($post['title']); ?></h3>
            <p class="body-serif" style="font-size:var(--font-size-sm);"><?php echo esc_html($post['excerpt']); ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<style>
@media (min-width: 768px) {
  .sk-podium-journal .sk-journal-grid { grid-template-columns: repeat(3, 1fr) !important; }
}
</style>
<?php endif; ?>
