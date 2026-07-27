<?php
/**
 * Sacred Kompass — Art for Peace Homepage Gallery Grid
 */
defined('ABSPATH') || exit;

$eyebrow = sk_option('art_eyebrow', 'Art Therapy');
$heading = sk_option('art_heading', 'Art for');
$heading_em = sk_option('art_heading_em', 'Peace');
$subheading = sk_option('art_sub', 'Artworks created as part of therapeutic healing, mindfulness and transformation. Explore our active collections below.');
$bg_image        = sk_option('art_bg_image', '');
$bg_image_mobile = sk_option('art_bg_image_mobile', '');

// Fetch art posts data from centralized cache helper
$art_posts_data = function_exists('sk_get_art_data') ? sk_get_art_data(24) : [];

$art_styles = [];
if ($bg_image) {
    $art_styles[] = 'background-image:url(\'' . esc_url($bg_image) . '\')';
    $art_styles[] = '--art-bg:url(\'' . esc_url($bg_image) . '\')';
}
if ($bg_image_mobile) {
    $art_styles[] = '--art-bg-mobile:url(\'' . esc_url($bg_image_mobile) . '\')';
}
$section_style = !empty($art_styles) ? ' style="' . implode(';', $art_styles) . ';"' : '';

?>
<section id="art" class="sk-art-section"<?php echo $section_style; ?>>
  <div class="wrap">
    <?php if ($eyebrow || $heading || $heading_em || $subheading): ?>
      <div class="sk-art-header">
        <?php if ($eyebrow): ?>
          <div class="eyebrow eyebrow-c"><?php echo esc_html($eyebrow); ?></div>
        <?php endif; ?>
        <?php if ($heading || $heading_em): ?>
          <h2 class="display-h2 sk-art-title">
            <?php 
            echo esc_html($heading);
            if ($heading_em) {
                echo ' <em>' . esc_html($heading_em) . '</em>';
            }
            ?>
          </h2>
        <?php endif; ?>
        <?php if ($subheading): ?>
          <p class="body-serif sk-art-sub"><?php echo esc_html($subheading); ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="sk-art-grid">
      <?php foreach ($art_posts_data as $item): ?>
        <a href="<?php echo esc_url($item['permalink']); ?>" class="sk-art-card">
          <div class="sk-art-card__frame">
            <?php if ($item['image_url']): ?>
              <img src="<?php echo esc_url($item['image_url']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy" />
            <?php endif; ?>
          </div>
          <div class="sk-art-card__meta">
            <h3 class="sk-art-card__title"><?php echo esc_html($item['title']); ?></h3>
            <?php if ($item['tag']): ?>
              <span class="sk-art-card__tag"><?php echo esc_html($item['tag']); ?></span>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if (count($art_posts_data) > 3): ?>
      <div class="sk-art-nav" aria-label="Art Gallery Navigation">
        <button class="sk-art-nav-arrow sk-art-prev" aria-label="Previous artworks" disabled>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <div class="sk-art-nav-dots"></div>
        <button class="sk-art-nav-arrow sk-art-next" aria-label="Next artworks">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
      </div>
    <?php endif; ?>

    <?php
    $art_gallery_page_id = sk_get_page_id_by_path('art-for-peace') ?: sk_get_page_id_by_path('art-gallery') ?: sk_get_page_id_by_path('art');
    $art_gallery_url = $art_gallery_page_id ? get_permalink($art_gallery_page_id) : home_url('/art-for-peace/');
    ?>
    <div class="sk-art-mobile-cta">
      <a href="<?php echo esc_url($art_gallery_url); ?>" class="btn btn-primary">
        <span>View Full Gallery</span>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 0.35rem;"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>
