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

?>
<section id="art" class="sk-podium-art section-py" style="background-color:var(--color-surface-base); border-top:1px solid var(--color-surface-strong);">
  <div class="wrap stagger-children">
    <span class="eyebrow"><?php echo esc_html($eyebrow); ?></span>
    <h2 class="display-h3"><?php echo esc_html($heading); ?> <?php echo esc_html($heading_em); ?></h2>
    <p class="body-serif" style="margin-bottom:var(--space-2);"><?php echo esc_html($subheading); ?></p>

    <div class="sk-art-grid">
      <?php foreach ($art_posts_data as $idx => $item): ?>
        <a href="<?php echo esc_url($item['permalink']); ?>" class="sk-art-card klimt-hover-reveal">
          <div class="klimt-image-wrapper">
             <?php if ($item['image_url']): ?>
               <img src="<?php echo esc_url($item['image_url']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy" />
             <?php else: ?>
               <div class="placeholder-img"></div>
             <?php endif; ?>
          </div>
          <div class="sk-art-meta">
            <h3 class="body-ui"><?php echo esc_html($item['title']); ?></h3>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
