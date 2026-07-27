<?php
/**
 * Template Name: Art Gallery
 */
defined('ABSPATH') || exit;

get_header();

// Fetch art posts data from centralized cache helper
$art_posts_data = function_exists('sk_get_art_data') ? sk_get_art_data(50) : [];

$eyebrow = sk_option('art_eyebrow', 'Art Therapy');
$heading = sk_option('art_heading', 'Art for');
$heading_em = sk_option('art_heading_em', 'Peace');
$subheading = sk_option('art_sub', 'Artworks created as part of therapeutic healing, mindfulness and transformation. Explore our active collections below.');
?>

<main class="sk-art-gallery-pg" style="padding-top: clamp(6rem, 10vw, 8rem); padding-bottom: clamp(6rem, 10vw, 8rem);">
  <div class="wrap">
    
    <header class="sk-art-header">
      <?php if ($eyebrow): ?>
        <div class="eyebrow eyebrow-c"><?php echo esc_html($eyebrow); ?></div>
      <?php endif; ?>
      <?php if ($heading || $heading_em): ?>
        <h1 class="display-h2 sk-art-title">
          <?php 
          echo esc_html($heading);
          if ($heading_em) {
              echo ' <em>' . esc_html($heading_em) . '</em>';
          }
          ?>
        </h1>
      <?php endif; ?>
      <?php if ($subheading): ?>
        <p class="body-serif sk-art-sub"><?php echo esc_html($subheading); ?></p>
      <?php endif; ?>
    </header>

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

  </div>
</main>

<?php
get_footer();
